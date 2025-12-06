<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Penjualan #{{ $sales->nomor_sales }}</title>

<style>
@media print {
    @page {
        size: 235mm 148mm; /* continuous form */
        margin: 4mm;
    }
}

body {
    font-family: "Courier New", monospace;
    font-size: 12px;
    line-height: 1.25;
}

.wrapper {
    width: 230mm;
    margin: auto;
}

table { width: 100%; border-collapse: collapse; }

.line {
    border-bottom: 1px dashed #000;
    margin: 2px 0;
}

.header td {
    vertical-align: top;
    padding-bottom: 4px;
}
</style>
</head>

<body>

<div class="wrapper">

<!-- TITLE TENGAH -->
<div style="text-align:center; margin-bottom:10px;">
    <strong>INVOICE PENJUALAN</strong><br>
    No: {{ $sales->nomor_sales }}
</div>

<!-- HEADER MIRIP INVOICE SERVICE -->
<table class="header">
    <tr>
        <td style="width:50%;">
            Tanggal : {{ \Carbon\Carbon::parse($sales->sales_date)->format('d M Y') }}<br>
            Nama : {{ $sales->client->nama_client ?? '-' }}<br>
            Telp : {{ $sales->client->no_telp ?? '-' }}<br>
            Status : {{ $sales->total_paid >= $sales->total ? 'LUNAS' : 'BELUM LUNAS' }}
        </td>

        <td style="width:50%; text-align:right;">
            <b>ARMOR GARAGE - SHOWROOM & SERVICE</b><br>
            Jl. Raya Bancaran<br>
            Bangkalan, Jawa Timur<br>
            0895-2354-1922
        </td>
    </tr>
</table>

<div class="line"></div>

<!-- TABEL BARANG MIRIP STRUK -->
<table>
    <tr style="border-bottom:1px dashed #000; font-weight:bold;">
        <td style="width:60%;">Nama Barang</td>
        <td style="width:10%; text-align:center;">Qty</td>
        <td style="width:30%; text-align:right;">Subtotal</td>
    </tr>

    @php $grandTotal = 0; @endphp
    @foreach($sales->items as $item)
        @php $grandTotal += $item->subtotal; @endphp
        <tr>
            <td>{{ $item->barang->nama_barang }} {{ $item->barang->merk_barang ?? '' }}</td>
            <td style="text-align:center;">{{ $item->qty }}</td>
            <td style="text-align:right;">{{ number_format($item->subtotal,0,',','.') }}</td>
        </tr>
    @endforeach
</table>

<div class="line"></div>

<!-- TOTAL + SOSMED/KETERANGAN DALAM SATU TABEL -->
<table style="width:100%; line-height:1.2;">
    <tr>
        <!-- KIRI -->
        <td style="width:65%; vertical-align:top;">
            Rek BCA No: 185.1538.661 a/n Amarullah Rizki<br>
            Rek BRI No: 610701006387501 a/n Amarullah Rizki<br>
            IG: @armor.garage | TikTok: @armor.garage
        </td>

        <!-- KANAN -->
        <td style="width:20%;">Total</td>
        <td style="width:15%; text-align:right;">
            {{ number_format($grandTotal,0,',','.') }}
        </td>
    </tr>

    <tr>
        <td></td>
        <td>Dibayar</td>
        <td style="text-align:right;">
            {{ number_format($sales->total_paid,0,',','.') }}
        </td>
    </tr>

    <tr>
        <td></td>
        <td>Sisa</td>
        <td style="text-align:right;">
            {{ number_format($sales->due_amount,0,',','.') }}
        </td>
    </tr>

    <tr>
        <td></td>
        <td><b>Kembali</b></td>
        <td style="text-align:right;">
            <b>{{ number_format($sales->payments->last()->change_amount ?? 0,0,',','.') }}</b>
        </td>
    </tr>
</table>

<div class="line"></div>

<div style="text-align:center; margin-top:4px; font-weight:bold;">
    TERIMA KASIH ATAS PEMBELIAN ANDA!
</div>

</div>

<script>
window.onload = () => window.print();
</script>
</body>
</html>
