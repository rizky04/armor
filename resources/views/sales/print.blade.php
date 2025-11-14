<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan #{{ $sales->nomor_sales }}</title>
    <style>
    @media print {
        @page { size: 80mm auto; margin: 0; }
        body { margin: 0; font-family: 'Courier New', monospace; font-size: 11px; }
        .no-print { display: none; }
    }

    body {
        background: #fff;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        line-height: 1.4;
        font-weight: bold; /* Semua teks bold */
    }

    .invoice-wrapper {
        width: 80mm;
        margin: 10px auto;
        padding: 10px;
        border: 1px solid #eee;
        background: #fff;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .dotted { border-bottom: 1px dotted #000; margin: 5px 0; }
    hr.dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }

    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 4px 0; }

    /* Tambahan untuk memperjelas item */
    .item-row td {
        border-bottom: 1px dotted #000;
        padding: 4px 0;
    }
    .item-name {
        text-transform: uppercase;
    }
    </style>
</head>
<body>
<div class="invoice-wrapper">

    <!-- HEADER -->
    <div class="text-center">
        <h5 class="mb-1">KARISMA MOTOR</h5>
        <p class="mb-1">Jl. Raya Nyorondung No. 96, Pamorah, Bangkalan</p>
        <p class="mb-1">Telp: 0878 - 4513 - 3640</p>
        <hr class="dashed">
        <p>INVOICE PENJUALAN</p>
        <p>No: {{ $sales->nomor_sales }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($sales->sales_date)->format('d/m/Y') }}</p>
        <hr class="dashed">
    </div>

    <!-- CUSTOMER INFO -->
    <div>
        <p class="mb-1">Nama: {{ $sales->client->nama_client ?? '-' }}</p>
        <p class="mb-1">Telp: {{ $sales->client->no_telp ?? '-' }}</p>
        <p class="mb-1">Status Bayar: {{ $sales->total_paid >= $sales->total ? 'LUNAS' : 'BELUM LUNAS' }}</p>
        <hr class="dotted">
    </div>

    <!-- BARANG -->
    <p class="mb-1">BARANG / PRODUK:</p>
    <table class="table">
        <thead>
            <tr>
                <th>NAMA</th>
                 <th>QTY</th>
                <th class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($sales->items as $item)
                @php $grandTotal += $item->subtotal; @endphp
                <tr class="item-row">
                    <td class="item-name">{{ $item->barang->nama_barang ?? '-' }} {{ $item->barang->merk_barang ?? '-' }} {{ $item->barang->jenis ?? '-' }} {{ $item->barang->keterangan ?? '-' }}</td>
                    <td class="item-name">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTAL -->
    <hr class="dashed">
    <table class="table">
        <tr>
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>DIBAYAR</td>
            <td class="text-right">Rp {{ number_format($sales->total_paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>SISA</td>
            <td class="text-right">Rp {{ number_format($sales->due_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>KEMBALI</td>
            <td class="text-right">Rp {{ number_format($sales->payments->last()->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr class="dotted">
    <p class="text-center">TERIMA KASIH ATAS KEPERCAYAAN ANDA!</p>
</div>
</body>
<script>
    window.onload = () => {
        window.print();
    };
    window.onafterprint = () => {
        window.close();
    };
</script>

</html>
