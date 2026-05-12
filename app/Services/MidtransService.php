<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create a Snap Transaction for the given Order and insert a Payment record.
     * 
     * @param Order $order
     * @return Payment|null
     */
    public function createSnapTransaction(Order $order)
    {
        // Calculate items
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => $item->menu_id,
                'price'    => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->menu_name, 0, 50)
            ];
        }

        if ($order->discount_amount > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -((int) $order->discount_amount),
                'quantity' => 1,
                'name'     => 'Discount (' . ($order->discount ? $order->discount->code : 'Manual') . ')'
            ];
        }

        // Add tax as a positive item if processing amount
        if ($order->tax_amount > 0) {
            $itemDetails[] = [
                'id'       => 'TAX',
                'price'    => (int) $order->tax_amount,
                'quantity' => 1,
                'name'     => 'Pajak / Tax'
            ];
        }

        $customerDetails = [
            'first_name' => $order->customer_name,
            'phone'      => $order->customer_phone,
        ];
        
        if (!empty($order->customer_email) && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            $customerDetails['email'] = $order->customer_email;
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
            'callbacks'           => [
                'finish'   => route('payment.finish'),
                'error'    => route('landing'),
                'unfinish' => route('landing')
            ],
            'notification_url'    => route('payment.callback')
        ];

        try {
            $snapResponse = Snap::createTransaction($params);

            // Create payment record
            $payment = Payment::create([
                'order_id'               => $order->id,
                'payment_gateway'        => 'midtrans',
                'gateway_order_id'       => $params['transaction_details']['order_id'],
                'snap_token'             => $snapResponse->token,
                'redirect_url'           => $snapResponse->redirect_url,
                'amount'                 => $order->total_amount,
                'status'                 => 'pending',
                'expires_at'             => now()->addHours(24) // Default Snap expiry is 24h
            ]);

            return $payment;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return null;
        }
    }
}
