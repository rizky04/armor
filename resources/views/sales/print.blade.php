<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan #{{ $sales->nomor_sales }}</title>

   <style>
    @media print {
        @page {
            size: 241mm auto; /* ukuran continuous form dot matrix */
            margin: 0;
        }
    }

    body {
        width: 235mm; /* <── dikurangi dari 241mm supaya ada jarak kiri kanan */
        margin: 0 auto;  /* center otomatis */
        padding: 5mm 8mm; /* <── ruang kanan kiri */
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.25;
    }

    .center { text-align: center; }
    .right { text-align: right; }
    .bold { font-weight: bold; }

    .line {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table td {
        padding: 3px 2px;
        vertical-align: top;
    }
</style>

</head>
<body>

    <!-- HEADER -->
    <div class="center bold" style="font-size: 15px;">ARMOR MOTOR</div>
    <div class="center">Jl. Raya Nyorondung No. 96, Pamorah</div>
    <div class="center">Bangkalan | Telp: 0878-4513-3640</div>

    <div class="line"></div>

    <div class="bold center">INVOICE</div>

    <table>
        <tr>
            <td>No Invoice</td><td>:</td><td>{{ $sales->nomor_sales }}</td>
            <td class="right">Tanggal : {{ \Carbon\Carbon::parse($sales->sales_date)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Nama</td><td>:</td><td>{{ $sales->client->nama_client ?? '-' }}</td></tr>
        <tr><td>Telp</td><td>:</td><td>{{ $sales->client->no_telp ?? '-' }}</td></tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $sales->total_paid >= $sales->total ? 'LUNAS' : 'BELUM LUNAS' }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="bold">Nama Barang</td>
            <td class="bold center">Qty</td>
            <td class="bold right">Subtotal</td>
        </tr>

        @php $grandTotal = 0; @endphp
        @foreach($sales->items as $item)
            @php $grandTotal += $item->subtotal; @endphp
            <tr>
                <td>{{ $item->barang->nama_barang ?? '' }} {{ $item->barang->merk_barang ?? '' }}</td>
                <td class="center">{{ $item->qty }}</td>
                <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Total</td><td class="right bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td></tr>
        <tr><td>Dibayar</td><td class="right bold">Rp {{ number_format($sales->total_paid, 0, ',', '.') }}</td></tr>
        <tr><td>Sisa</td><td class="right bold">Rp {{ number_format($sales->due_amount, 0, ',', '.') }}</td></tr>
        <tr>
            <td>Kembali</td>
            <td class="right bold">Rp {{ number_format($sales->payments->last()->change_amount ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="center bold" style="margin-top: 5px;">
        Terima kasih atas kepercayaan Anda!
    </div>

</body>

<script>
    window.onload = () => window.print();
</script>

</html>
