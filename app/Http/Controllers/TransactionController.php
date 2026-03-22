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

        return view('transaksi.index', compact('menus', 'categories'));
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
            'payment_method'   => 'required|in:cash,transfer,qris,e-wallet',
            'payment_status'   => 'required|in:paid,unpaid',
            'status'           => 'required|in:confirmed,processing,ready,completed',
            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
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

                $order = Order::create([
                    'order_number'    => Order::generateOrderNumber(),
                    'user_id'         => $request->user()->id,
                    'customer_name'   => $validated['customer_name'],
                    'customer_email'  => '-',
                    'customer_phone'  => $validated['customer_phone'],
                    'pickup_time'     => $validated['pickup_time'] ?? now(),
                    'notes'           => $validated['notes'] ?? null,
                    'subtotal'        => $subtotal,
                    'discount_amount' => 0,
                    'tax_amount'      => 0,
                    'total_amount'    => $subtotal,
                    'status'          => $validated['status'],
                    'payment_status'  => $validated['payment_status'],
                    'payment_method'  => $validated['payment_method'],
                    'completed_at'    => $validated['status'] === 'completed' ? now() : null,
                ]);

                foreach ($itemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                return $order;
            });

            return redirect()->back()->with([
                'success' => "Transaksi #{$order->order_number} berhasil! Total: Rp " . number_format($order->total_amount, 0, ',', '.'),
                'receipt' => $order->load('items')->toArray(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
