<x-mail::message>
# Halo {{ $order->customer_name }},

Status pesanan Anda dengan nomor **{{ $order->order_number }}** telah diperbarui.

**Status Saat Ini:** {{ strtoupper($order->status) }}

@if($order->status === 'cancelled' && $order->cancel_reason)
**Alasan Pembatalan:** {{ $order->cancel_reason }}
@endif

Terima kasih telah mempercayakan pesanan Anda kepada kami. Proses kami terus ditingkatkan untuk memastikan pelayanan yang terbaik bagi Anda. 

<x-mail::button :url="config('app.url')">
Kunjungi Website Kami
</x-mail::button>

Salam hangat,<br>
Tim {{ config('app.name') }}
</x-mail::message>
