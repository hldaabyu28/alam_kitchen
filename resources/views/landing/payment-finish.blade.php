<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran | Alam Kitchen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#FDFBF7] antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-xl shadow-orange-900/5 border border-orange-100/50 p-10 text-center relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-orange-50 rounded-full opacity-50"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-orange-50 rounded-full opacity-50"></div>

            <div class="relative z-10">
                @php
                $rawStatus = $payment ? $payment->status : request('transaction_status');
                $isSuccess = in_array($rawStatus, ['settlement', 'capture', 'paid', 'success']);
                $isPending = in_array($rawStatus, ['pending']);
                @endphp

                @if($isSuccess)
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Pembayaran Berhasil!</h1>
                <p class="text-gray-500 mb-8 leading-relaxed">Terima kasih atas pesanan Anda. Kami sedang memproses pesanan Anda sekarang.</p>

                @if($payment && $payment->order)
                <div class="bg-gray-50 rounded-2xl p-4 mb-8">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Nomor Pesanan</p>
                    <p class="text-gray-900 font-bold text-lg">{{ $payment->order->order_number }}</p>
                </div>
                @endif

                @elseif($isPending)
                <div class="w-24 h-24 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Menunggu Pembayaran</h1>
                <p class="text-gray-500 mb-8 leading-relaxed">Silakan selesaikan pembayaran Anda sesuai dengan instruksi dari Midtrans.</p>

                @if($payment && $payment->order)
                <div class="bg-gray-50 rounded-2xl p-4 mb-8">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-1">Nomor Pesanan</p>
                    <p class="text-gray-900 font-bold text-lg">{{ $payment->order->order_number }}</p>
                </div>
                @endif

                @else
                <div class="w-24 h-24 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Pembayaran Gagal</h1>
                <p class="text-gray-500 mb-8 leading-relaxed">Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi atau hubungi bantuan.</p>
                @endif

                <div class="space-y-3">
                    <a href="{{ route('landing') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-full transition duration-300 shadow-lg shadow-orange-500/20">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>