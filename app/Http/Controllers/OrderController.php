<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ]);

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

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'customer_name'   => $validated['customer_name'],
                'customer_email'  => $validated['customer_email'],
                'customer_phone'  => $validated['customer_phone'],
                'pickup_time'     => $validated['pickup_time'],
                'notes'           => $validated['notes'] ?? null,
                'subtotal'        => $subtotal,
                'discount_amount' => 0,
                'tax_amount'      => 0,
                'total_amount'    => $subtotal,
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
            ]);

            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });

        return redirect()->route('landing')->with('success', "Pesanan #{$order->order_number} berhasil dibuat! Silakan tunggu konfirmasi.");
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
        $order->load('items');

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

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
