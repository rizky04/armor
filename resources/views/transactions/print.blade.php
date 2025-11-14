{{-- <!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h2>Nota Transaksi</h2>
    <p><strong>Reference:</strong> {{ $transaction->reference }}</p>
    <p><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Customer:</strong> {{ $transaction->customer->name ?? 'Walk-in Customer' }}</p>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total:</strong> Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
    <p><strong>Diskon:</strong> Rp {{ number_format($transaction->discount, 0, ',', '.') }}</p>
    <p><strong>Pajak:</strong> Rp {{ number_format($transaction->tax, 0, ',', '.') }}</p>
    <p><strong>Total Akhir:</strong> Rp {{ number_format($transaction->total_after_tax, 0, ',', '.') }}</p>
    <p><strong>Bayar:</strong> Rp {{ number_format($transaction->cash, 0, ',', '.') }}</p>
    <p><strong>Kembali:</strong> Rp {{ number_format($transaction->change, 0, ',', '.') }}</p>
</body>
</html> --}}

<!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            width: 230px; /* 58mm approx */
            margin: 0 auto;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; }
    </style>
</head>
<body onload="window.print()">

    <div class="center">
        <h3 style="margin:0;">Kharisma CarWash</h3>
        <small>Jl. Contoh No. 123</small><br>
        <small>Telp: 08123456789</small>
    </div>

    <div class="line"></div>

    <p><strong>Ref:</strong> {{ $transaction->reference }}<br>
    <strong>Tgl:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
    <strong>Customer:</strong> {{ $transaction->customer->name ?? 'Walk-in' }}</p>

    <div class="line"></div>

    <table>
        @foreach($transaction->items as $item)
        <tr>
            <td colspan="2">{{ $item->product->name }}</td>
        </tr>
        <tr>
            <td>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Total</td>
            <td class="right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pajak</td>
            <td class="right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Akhir</strong></td>
            <td class="right"><strong>Rp {{ number_format($transaction->total_after_tax, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="right">Rp {{ number_format($transaction->cash, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="right">Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="center">
        <p>~~ Terima Kasih ~~</p>
        <small>Barang yang sudah dibeli<br>tidak dapat dikembalikan</small>
    </div>

</body>
</html>

