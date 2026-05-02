<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentCallbackController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
    }

    public function handleNotification(Request $request)
    {
        try {
            $notification = new \Midtrans\Notification();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Invalid'], 400);
        }

        $payment = Payment::where('gateway_order_id', $notification->order_id)->first();

        if (!$payment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $order = $payment->order;

        $status = $notification->transaction_status;

        switch ($status) {
            case 'capture':
            case 'settlement':
                $payment->status = 'paid';
                $payment->paid_at = now();
                $order->payment_status = 'paid';
                break;

            case 'pending':
                $payment->status = 'pending';
                break;

            case 'expire':
            case 'cancel':
            case 'deny':
                $payment->status = 'failed';
                $order->payment_status = 'failed';
                break;
        }

        $payment->save();
        $order->save();

        return response()->json(['message' => 'OK']);
    }
}
