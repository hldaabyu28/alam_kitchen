@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFBF7] flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-sm border border-orange-100 p-8 text-center">
        @php
        $status = $payment ? $payment->status : (request('transaction_status') == 'settlement' || request('transaction_status') == 'capture' ? 'paid' : (request('transaction_status') == 'pending' ? 'pending' : 'failed'));
        @endphp

        @if($status == 'paid')
        <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
        <p class="text-gray-500 mb-6">Terima kasih atas pesanan Anda. Kami sedang memproses pesanan Anda sekarang.</p>
        @if($payment && $payment->order)
        <p class="text-gray-600 font-semibold mb-6">No. Pesanan: {{ $payment->order->order_number }}</p>
        @endif
        @elseif($status == 'pending')
        <div class="w-20 h-20 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Menunggu Pembayaran!</h1>
        <p class="text-gray-500 mb-6">Silakan selesaikan pembayaran Anda melalui instruksi yang diberikan oleh Midtrans.</p>
        @if($payment && $payment->order)
        <p class="text-gray-600 font-semibold mb-6">No. Pesanan: {{ $payment->order->order_number }}</p>
        @endif
        @else
        <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Gagal / Dibatalkan</h1>
        <p class="text-gray-500 mb-6">Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi staf kami.</p>
        @endif

        <a href="{{ route('landing') }}" class="inline-block w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-full transition duration-300">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection