<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Show the POS transaction page.
     */
    public function index()
    {
        $menus = Menu::with('category')
            ->where('is_available', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::where('is_active', true)
            ->orderBy('order')
            ->get();

        $activeTax = \App\Models\Tax::where('is_active', true)->first();

        return view('transaksi.index', compact('menus', 'categories', 'activeTax'));
    }

    /**
     * Store a new order from the kasir POS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'pickup_time'      => 'nullable|date',
            'notes'            => 'nullable|string|max:1000',
            'discount_amount'  => 'nullable|numeric|min:0',
            'payment_method'   => 'required|in:cash,transfer,qris,e-wallet,midtrans',
            'payment_status'   => 'required|in:paid,unpaid',
            'status'           => 'required|in:confirmed,processing,ready,completed',
            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount_code'    => 'nullable|string',
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request) {
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
                
                $activeTax = \App\Models\Tax::where('is_active', true)->first();
                $taxRate = $activeTax ? $activeTax->rate : 0;
                $taxAmount = round(($subtotal - $discountAmount) * ($taxRate / 100));

                $totalAmount = $subtotal - $discountAmount + $taxAmount;

                $discountId = null;
                if (!empty($validated['discount_code'])) {
                    $discount = \App\Models\Discount::where('code', $validated['discount_code'])->first();
                    if ($discount && $discount->is_active) {
                        $discountId = $discount->id;
                        if ($discountAmount > 0) {
                            $discount->increment('used_count');
                        }
                    }
                }

                $order = Order::create([
                    'order_number'    => Order::generateOrderNumber(),
                    'user_id'         => $request->user()->id,
                    'customer_name'   => $validated['customer_name'],
                    'customer_email'  => '-',
                    'customer_phone'  => $validated['customer_phone'],
                    'pickup_time'     => $validated['pickup_time'] ?? now(),
                    'notes'           => $validated['notes'] ?? null,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discountAmount,
                    'discount_id'     => $discountId,
                    'tax_amount'      => $taxAmount,
                    'total_amount'    => $totalAmount,
                    'status'          => $validated['status'],
                    'payment_status'  => $validated['payment_method'] === 'midtrans' ? 'unpaid' : $validated['payment_status'],
                    'payment_method'  => $validated['payment_method'],
                    'completed_at'    => ($validated['status'] === 'completed' && $validated['payment_method'] !== 'midtrans') ? now() : null,
                ]);

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            if ($validated['payment_method'] === 'midtrans') {
                $midtrans = new \App\Services\MidtransService();
                $payment = $midtrans->createSnapTransaction($order);
                
                if ($payment && $payment->snap_token) {
                    return redirect()->back()->with([
                        'success'          => "Transaksi #{$order->order_number} dibuat. Menunggu pembayaran...",
                        'snap_token'       => $payment->snap_token,
                        'midtrans_order_id' => $order->id,
                        'receipt'          => $order->load('items')->toArray(),
                    ]);
                }
            }

            return redirect()->back()->with([
                'success' => "Transaksi #{$order->order_number} berhasil! Total: Rp " . number_format($order->total_amount, 0, ',', '.'),
                'receipt' => $order->load('items')->toArray(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark midtrans payment as success from local kasir.
     */
    public function markMidtransSuccess(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->payment_status = 'paid';
            $order->completed_at   = now();
            $order->save();

            $payment = \App\Models\Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->status = 'settlement';
                $payment->save();
            }
        });

        session()->flash('success', "Pembayaran Midtrans berhasil untuk Pesanan #{$order->order_number}");
        session()->flash('receipt', $order->load('items')->toArray());

        return response()->json(['success' => true]);
    }

    /**
     * Print the order receipt.
     */
    public function print(Order $order)
    {
        $order->load(['items', 'user']);
        return view('transaksi.print', compact('order'));
    }
}
