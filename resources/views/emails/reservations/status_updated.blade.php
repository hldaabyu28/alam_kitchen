<x-mail::message>
# Halo {{ $reservation->customer_name }},

Status reservasi meja Anda pada tanggal **{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}** pukul **{{ $reservation->reservation_time_slot }}** telah diperbarui.

**Info Meja:** Meja #{{ $reservation->table->table_number ?? '-' }} (Kapasitas: {{ $reservation->guest_count }} orang)  
**Status Saat Ini:** {{ strtoupper($reservation->status) }}

@if($reservation->status === 'cancelled' && $reservation->cancel_reason)
**Alasan Pembatalan:** {{ $reservation->cancel_reason }}
@endif

Terima kasih telah melakukan reservasi di tempat kami! Jika ada pertanyaan atau perubahan, silakan hubungi tim kami.

<x-mail::button :url="config('app.url')">
Kunjungi Website Kami
</x-mail::button>

Salam hangat,<br>
Tim {{ config('app.name') }}
</x-mail::message>
