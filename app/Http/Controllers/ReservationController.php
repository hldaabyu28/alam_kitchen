<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use App\Mail\ReservationStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ReservationController extends Controller
{
    /**
     * Display reservation management page.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['table', 'orders', 'orders.items'])
            ->withCount('items')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time_slot', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query->get();

        $tables = Table::where('is_active', true)
            ->orderBy('table_number')
            ->get();

        $menus = Menu::with('category')
            ->where('is_available', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::where('is_active', true)
            ->orderBy('order')
            ->get();
        return view('reservasi.index', compact('reservations', 'tables', 'menus', 'categories'));
    }

    /**
     * Store a new reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'        => 'required|string|max:255',
            'customer_phone'       => 'required|string|max:20',
            'customer_email'       => 'nullable|email|max:255',
            'table_id'             => 'required|exists:tables,id',
            'reservation_date'     => 'required|date|after_or_equal:today',
            'reservation_time_slot' => 'required|date_format:H:i',
            'guest_count'          => 'required|integer|min:1',
            'special_requests'     => 'nullable|string|max:1000',
        ]);

        // Check table capacity
        $table = Table::findOrFail($validated['table_id']);
        if ($validated['guest_count'] > $table->capacity) {
            return redirect()->back()->with('error', "Meja #{$table->table_number} hanya dapat menampung {$table->capacity} orang.");
        }

        // Check if table is available at the requested date/time
        $conflicting = Reservation::where('table_id', $validated['table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('reservation_time_slot', $validated['reservation_time_slot'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflicting) {
            return redirect()->back()->with('error', 'Meja sudah direservasi pada waktu tersebut. Silakan pilih waktu lain.');
        }

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        // Calculate end_time (default 2 hours)
        $startDateTime = $validated['reservation_date'] . ' ' . $validated['reservation_time_slot'];
        $validated['end_time'] = \Carbon\Carbon::parse($startDateTime)->addHours(2);

        $reservation = Reservation::create($validated);

        // Notify Admin & Super Admin
        $admins = User::role(['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewReservationNotification($reservation));
        }

        // Update table status to reserved
        $table->update(['status' => 'reserved']);

        return redirect()->back()->with('success', "Reservasi untuk {$reservation->customer_name} berhasil dibuat!");
    }

    /**
     * Store a reservation from the landing page (no auth required).
     * Optionally also creates an order with pre-selected menu items.
     */
    public function guestStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name'        => 'required|string|max:255',
            'customer_phone'       => 'required|string|max:20',
            'customer_email'       => 'nullable|email|max:255',
            'table_id'             => 'required|exists:tables,id',
            'reservation_date'     => 'required|date|after_or_equal:today',
            'reservation_time_slot' => 'required|date_format:H:i',
            'guest_count'          => 'required|integer|min:1',
            'special_requests'     => 'nullable|string|max:1000',
            'items'                => 'nullable|array',
            'items.*.menu_id'      => 'required_with:items|exists:menus,id',
            'items.*.quantity'     => 'required_with:items|integer|min:1',
            'discount_code'        => 'nullable|string',
        ]);

        $storeObj = \App\Models\Store::first();
        if ($storeObj && !$storeObj->is_active) {
            return redirect()->back()
                ->withInput()
                ->with('booking_error', "Mohon maaf, toko sedang tutup.");
        }

        // Check table capacity
        $table = Table::findOrFail($validated['table_id']);
        if ($validated['guest_count'] > $table->capacity) {
            return redirect()->back()
                ->withInput()
                ->with('booking_error', "Meja #{$table->table_number} hanya menampung {$table->capacity} orang.");
        }

        // Check availability
        $conflicting = Reservation::where('table_id', $validated['table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('reservation_time_slot', $validated['reservation_time_slot'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflicting) {
            return redirect()->back()
                ->withInput()
                ->with('booking_error', 'Meja sudah direservasi pada waktu tersebut.');
        }

        try {
            $result = DB::transaction(function () use ($validated, $table) {
                // Create reservation
                $rsvData = collect($validated)->except('items')->toArray();
                $rsvData['status'] = 'pending';
                $startDateTime = $rsvData['reservation_date'] . ' ' . $rsvData['reservation_time_slot'];
                $rsvData['end_time'] = \Carbon\Carbon::parse($startDateTime)->addHours(2);

                $reservation = Reservation::create($rsvData);

                // Notify Admin & Super Admin
                $admins = User::role(['admin', 'super_admin', 'kasir'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new NewReservationNotification($reservation));
                }

                // Update table status to reserved
                $table->update(['status' => 'reserved']);

                $order = null;

                // If menu items were added, create an order
                if (!empty($validated['items'])) {
                    $subtotal = 0;
                    $itemsData = [];

                    foreach ($validated['items'] as $item) {
                        $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                        $qty = $item['quantity'];

                        if ($menu->stock !== null && $menu->stock < $qty) {
                            throw new \Exception("Stok {$menu->name} tidak mencukupi (tersisa {$menu->stock}).");
                        }

                        if ($menu->stock !== null) {
                            $menu->decrement('stock', $qty);
                        }

                        $itemSubtotal = $menu->price * $qty;
                        $subtotal += $itemSubtotal;

                        $itemsData[] = [
                            'menu_id'    => $menu->id,
                            'menu_name'  => $menu->name,
                            'unit_price' => $menu->price,
                            'quantity'   => $qty,
                            'subtotal'   => $itemSubtotal,
                        ];
                    }

                    $discountAmount = 0;
                    $discountId = null;

                    if (!empty($validated['discount_code'])) {
                        $discount = \App\Models\Discount::where('code', $validated['discount_code'])->first();
                        if ($discount && $discount->is_active && $subtotal >= ($discount->min_order_amount ?? 0)) {
                            if (now()->gte($discount->valid_from) && now()->lte($discount->valid_until)) {
                                if ($discount->usage_limit === null || $discount->used_count < $discount->usage_limit) {
                                    if ($discount->type === 'percentage') {
                                        $calc = ($discount->percentage / 100) * $subtotal;
                                        $discountAmount = $discount->max_discount_amount > 0 ? min($calc, $discount->max_discount_amount) : $calc;
                                    } else {
                                        $discountAmount = $discount->amount;
                                    }
                                    $discountAmount = min($discountAmount, $subtotal);
                                    $discountId = $discount->id;

                                    $discount->increment('used_count');
                                }
                            }
                        }
                    }

                    $order = Order::create([
                        'order_number'    => Order::generateOrderNumber(),
                        'reservation_id'  => $reservation->id,
                        'customer_name'   => $validated['customer_name'],
                        'customer_email'  => $validated['customer_email'] ?? '-',
                        'customer_phone'  => $validated['customer_phone'],
                        'pickup_time'     => \Carbon\Carbon::parse($startDateTime),
                        'notes'           => $validated['special_requests'] ?? null,
                        'subtotal'        => $subtotal,
                        'discount_amount' => $discountAmount,
                        'discount_id'     => $discountId,
                        'tax_amount'      => 0,
                        'total_amount'    => $subtotal - $discountAmount,
                        'status'          => 'confirmed',
                        'payment_status'  => 'unpaid',
                        'payment_method'  => 'cash',
                    ]);

                    foreach ($itemsData as $itemData) {
                        $order->items()->create($itemData);
                    }
                }

                return ['reservation' => $reservation, 'order' => $order, 'table' => $table];
            });

            $msg = "Reservasi berhasil! Meja #{$result['table']->table_number} pada "
                . \Carbon\Carbon::parse($validated['reservation_date'])->format('d M Y')
                . " pukul {$validated['reservation_time_slot']}.";

            if ($result['order']) {
                $msg .= " Pesanan senilai Rp " . number_format($result['order']->total_amount, 0, ',', '.') . " telah dibuat.";
            }

            // If an order exists, redirect to payment
            if ($result['order']) {
                $midtrans = new \App\Services\MidtransService();
                $payment = $midtrans->createSnapTransaction($result['order']);
                if ($payment && $payment->redirect_url) {
                    // Set flash data to be displayed when returning from Midtrans, or just rely on the new payment finish page
                    session()->flash('success', $msg);
                    return redirect($payment->redirect_url);
                }
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('booking_error', $e->getMessage());
        }
    }

    /**
     * Show reservation detail (JSON for modal).
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['table', 'orders.items', 'orders.payment']);

        return response()->json($reservation);
    }

    /**
     * Update reservation status.
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status'        => 'required|in:confirmed,cancelled,completed',
            'cancel_reason' => 'required_if:status,cancelled|nullable|string|max:500',
        ]);

        DB::transaction(function () use ($reservation, $validated) {
            $reservation->status = $validated['status'];

            if ($validated['status'] === 'confirmed') {
                $reservation->confirmed_at = now();
                // Set table to reserved
                if ($reservation->table) {
                    $reservation->table->update(['status' => 'reserved']);
                }
            }

            if ($validated['status'] === 'cancelled') {
                $reservation->cancelled_at = now();
                $reservation->cancel_reason = $validated['cancel_reason'];
                // Free the table
                if ($reservation->table) {
                    $reservation->table->update(['status' => 'available']);
                }
            }

            if ($validated['status'] === 'completed') {
                // Free the table
                if ($reservation->table) {
                    $reservation->table->update(['status' => 'available']);
                }
            }

            $reservation->save();
        });

        // Send Email Notification
        if (!empty($reservation->customer_email) && filter_var($reservation->customer_email, FILTER_VALIDATE_EMAIL) && $reservation->customer_email !== '-') {
            try {
                Mail::to($reservation->customer_email)->send(new ReservationStatusUpdated($reservation));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email update reservasi: ' . $e->getMessage());
            }
        }

        $statusLabels = [
            'confirmed' => 'dikonfirmasi',
            'cancelled' => 'dibatalkan',
            'completed' => 'diselesaikan',
        ];

        return redirect()->back()->with('success', "Reservasi berhasil {$statusLabels[$validated['status']]}!");
    }

    /**
     * Add an order to a reservation (with menu items).
     */
    public function addOrder(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'payment_method'   => 'required|in:cash,transfer,qris,e-wallet',
            'payment_status'   => 'required|in:paid,unpaid',
            'status'           => 'required|in:confirmed,processing,ready,completed',
            'notes'            => 'nullable|string|max:1000',
            'discount_amount'  => 'nullable|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request, $reservation) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                    $qty = $item['quantity'];

                    if ($menu->stock !== null && $menu->stock < $qty) {
                        throw new \Exception("Stok {$menu->name} tidak mencukupi (tersisa {$menu->stock}).");
                    }

                    if ($menu->stock !== null) {
                        $menu->decrement('stock', $qty);
                    }

                    $itemSubtotal = $menu->price * $qty;
                    $subtotal += $itemSubtotal;

                    $itemsData[] = [
                        'menu_id'    => $menu->id,
                        'menu_name'  => $menu->name,
                        'unit_price' => $menu->price,
                        'quantity'   => $qty,
                        'subtotal'   => $itemSubtotal,
                    ];
                }

                $discountAmount = min($validated['discount_amount'] ?? 0, $subtotal);
                $totalAmount = $subtotal - $discountAmount;

                $order = Order::create([
                    'order_number'    => Order::generateOrderNumber(),
                    'user_id'         => $request->user()->id,
                    'reservation_id'  => $reservation->id,
                    'customer_name'   => $reservation->customer_name,
                    'customer_email'  => $reservation->customer_email ?? '-',
                    'customer_phone'  => $reservation->customer_phone,
                    'pickup_time'     => now(),
                    'notes'           => $validated['notes'] ?? null,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discountAmount,
                    'tax_amount'      => 0,
                    'total_amount'    => $totalAmount,
                    'status'          => $validated['status'],
                    'payment_status'  => $validated['payment_status'],
                    'payment_method'  => $validated['payment_method'],
                    'completed_at'    => $validated['status'] === 'completed' ? now() : null,
                ]);

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                // Set the table to occupied
                if ($reservation->table) {
                    $reservation->table->update(['status' => 'occupied']);
                }

                return $order;
            });

            return redirect()->back()->with('success', "Pesanan #{$order->order_number} berhasil ditambahkan ke reservasi! Total: Rp " . number_format($order->total_amount, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get available tables for a given date/time (JSON).
     */
    public function availableTables(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i',
            'guest_count' => 'required|integer|min:1',
        ]);

        // Get table IDs that are already reserved at this date/time
        $reservedTableIds = Reservation::where('reservation_date', $request->date)
            ->where('reservation_time_slot', $request->time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('table_id');

        $tables = Table::where('is_active', true)
            ->where('capacity', '>=', $request->guest_count)
            ->whereNotIn('id', $reservedTableIds)
            ->orderBy('table_number')
            ->get();

        return response()->json($tables);
    }
}
