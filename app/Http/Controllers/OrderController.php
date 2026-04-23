<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use App\Mail\OrderStatusUpdated;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;


class OrderController extends Controller
{
    /**
     * Store a new order from the landing page checkout.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'pickup_time'    => 'required|date|after:now',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes'    => 'nullable|string|max:500',
            'discount_code'    => 'nullable|string',
        ]);

        $storeObj = \App\Models\Store::first();
        if ($storeObj && !$storeObj->is_active) {
            return redirect()->route('landing')->with('error', "Mohon maaf, toko sedang tutup.");
        }

        try {
            $order = DB::transaction(function () use ($validated) {
                $subtotal = 0;
                $itemsData = [];

                foreach ($validated['items'] as $item) {
                    $menu = Menu::lockForUpdate()->findOrFail($item['menu_id']);
                    $qty = $item['quantity'];

                    // Check sufficient stock
                    if ($menu->stock !== null && $menu->stock < $qty) {
                        throw new \Exception("Stok {$menu->name} tidak mencukupi (tersisa {$menu->stock}).");
                    }

                    // Decrement stock
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
                        'notes'      => $item['notes'] ?? null,
                    ];
                }

                $discountAmount = 0;
                $discountId = null;

                if (!empty($validated['discount_code'])) {
                    $discount = Discount::where('code', $validated['discount_code'])->first();
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
                    'customer_name'   => $validated['customer_name'],
                    'customer_email'  => $validated['customer_email'],
                    'customer_phone'  => $validated['customer_phone'],
                    'pickup_time'     => $validated['pickup_time'],
                    'notes'           => $validated['notes'] ?? null,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discountAmount,
                    'discount_id'     => $discountId,
                    'tax_amount'      => 0,
                    'total_amount'    => $subtotal - $discountAmount,
                    'status'          => 'pending',
                    'payment_status'  => 'unpaid',
                ]);

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                // Notify Admin & Super Admin
                $admins = User::role(['admin', 'super_admin', 'kasir'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new NewOrderNotification($order));
                }

                return $order;
            });

            // Initialize Midtrans Payment
            $midtrans = new \App\Services\MidtransService();
            $payment = $midtrans->createSnapTransaction($order);

            if ($payment && $payment->redirect_url) {
                return redirect($payment->redirect_url);
            }

            return redirect()->route('landing')->with('success', "Pesanan #{$order->order_number} berhasil dibuat, namun gagal memuat halaman pembayaran. Silakan hubungi kasir.");
        } catch (\Exception $e) {
            return redirect()->route('landing')->with('error', $e->getMessage());
        }
    }

    /**
     * Dashboard: list all orders.
     */
    public function index(Request $request)
    {
        $query = Order::withCount('items')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return view('order.index', compact('orders'));
    }

    /**
     * Dashboard: show order detail (JSON for modal).
     */
    public function show(Order $order)
    {
        $order->load(['items', 'reservation', 'reservation.table']);

        return response()->json($order);
    }

    /**
     * Dashboard: update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'        => 'required|in:confirmed,processing,ready,completed,cancelled',
            'cancel_reason' => 'required_if:status,cancelled|nullable|string|max:500',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->status = $validated['status'];

            if ($validated['status'] === 'cancelled') {
                $order->cancelled_at = now();
                $order->cancel_reason = $validated['cancel_reason'];

                // Restore stock for each item
                $order->load('items');
                foreach ($order->items as $item) {
                    $menu = Menu::find($item->menu_id);
                    if ($menu && $menu->stock !== null) {
                        $menu->increment('stock', $item->quantity);
                    }
                }
            }

            if ($validated['status'] === 'completed') {
                $order->completed_at = now();
            }

            $order->save();
        });

        // Send Email Notification
        if (!empty($order->customer_email) && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL) && $order->customer_email !== '-') {
            try {
                Mail::to($order->customer_email)->send(new OrderStatusUpdated($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email update pesanan: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Dashboard: update order payment method and status.
     */
    public function updatePayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris,e-wallet',
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()->back()->with('success', 'Informasi pembayaran berhasil diperbarui!');
    }
}
