<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $order->order_number }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            width: 58mm; /* standard thermal printer width */
            margin: 0 auto;
            padding: 10px;
        }
        h2 { font-size: 16px; margin: 0; }
        p { margin: 3px 0; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .w-full { width: 100%; }
        .mb-2 { margin-bottom: 10px; }
        .mt-2 { margin-top: 10px; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .item-name { width: 100%; display: block; }
        
        @media print {
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="text-center border-bottom mb-2">
        <h2>Alam Kitchen</h2>
        <p>Jl. Contoh Alamat No. 123<br>Telp: 08123456789</p>
    </div>

    <div style="margin-bottom: 5px;">
        <div class="flex justify-between">
            <span>No:</span>
            <span>{{ $order->order_number }}</span>
        </div>
        <div class="flex justify-between">
            <span>Kasir:</span>
            <span>{{ $order->user->name ?? 'Kasir' }}</span>
        </div>
        <div class="flex justify-between">
            <span>Waktu:</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Customer:</span>
            <span>{{ $order->customer_name }}</span>
        </div>
    </div>

    <div class="border-top border-bottom">
        <table>
            @foreach($order->items as $item)
            <tr>
                <td colspan="3"><span class="item-name">{{ $item->menu_name }}</span></td>
            </tr>
            <tr>
                <td style="width: 25%;">{{ $item->quantity }}x</td>
                <td style="width: 35%;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="width: 40%;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="mb-2">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div class="flex justify-between">
            <span>Diskon:</span>
            <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($order->tax_amount > 0)
        <div class="flex justify-between">
            <span>Pajak ({{ App\Models\Tax::where('is_active', true)->value('rate') + 0 }}%):</span>
            <span>+{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="flex justify-between font-bold" style="font-size: 14px; margin-top: 5px;">
            <span>TOTAL:</span>
            <span>{{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="border-top mb-2">
        <div class="flex justify-between">
            <span>Bayar ({{ ucfirst($order->payment_method) }}):</span>
            <span>{{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Status:</span>
            <span>{{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
        </div>
    </div>

    <div class="text-center border-top mt-2" style="font-size: 10px;">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Silakan datang kembali.</p>
    </div>

</body>
</html>
