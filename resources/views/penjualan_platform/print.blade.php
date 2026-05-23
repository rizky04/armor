<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota {{ $penjualan->nomor_penjualan }}</title>
<style>
    @media print { @page { size: A4 portrait; margin: 8mm; } .no-print { display: none; } }
    body { font-family: "Courier New", monospace; font-size: 12px; line-height: 1.4; }
    .wrapper { width: 190mm; margin: auto; }
    table { width: 100%; border-collapse: collapse; }
    .line { border-bottom: 1px dashed #000; margin: 5px 0; }
    td { padding: 2px 4px; }
    .tr { text-align: right; }
    .tc { text-align: center; }
    .no-print { text-align: center; margin-bottom: 12px; }
    .no-print button { padding: 8px 20px; font-size: 14px; cursor: pointer; margin: 0 4px; }
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨 Print</button>
    <button onclick="window.close()">✕ Tutup</button>
</div>

<div class="wrapper">

    <div class="tc" style="margin-bottom:8px;">
        <strong style="font-size:14px;">ARMOR GARAGE</strong><br>
        Jl. Raya Bancaran, Bangkalan | 0895-2354-1922
    </div>
    <div class="line"></div>

    <table>
        <tr>
            <td style="width:50%">
                No     : {{ $penjualan->nomor_penjualan }}<br>
                Tgl    : {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}<br>
                Platform: {{ $penjualan->platform }}
            </td>
            <td class="tr">
                @if($penjualan->nomor_pesanan)No. Pesanan: {{ $penjualan->nomor_pesanan }}<br>@endif
                @if($penjualan->nama_pembeli)Pembeli: {{ $penjualan->nama_pembeli }}@endif
            </td>
        </tr>
    </table>
    <div class="line"></div>

    <table>
        <tr style="border-bottom:1px dashed #000; font-weight:bold;">
            <td style="width:40%">Barang</td>
            <td class="tr" style="width:15%">Kulak</td>
            <td class="tr" style="width:15%">Jual</td>
            <td class="tc" style="width:8%">Qty</td>
            <td class="tr" style="width:22%">Subtotal Jual</td>
        </tr>
        @foreach($penjualan->items as $item)
        <tr>
            <td>{{ $item->nama_barang }}</td>
            <td class="tr">{{ number_format($item->harga_kulak,0,',','.') }}</td>
            <td class="tr">{{ number_format($item->harga_jual,0,',','.') }}</td>
            <td class="tc">{{ $item->qty }}</td>
            <td class="tr">{{ number_format($item->subtotal_jual,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>
    <div class="line"></div>

    <table>
        <tr><td style="width:60%"></td><td>Total Harga Jual</td><td class="tr">{{ number_format($penjualan->total_harga_jual,0,',','.') }}</td></tr>
        @if($penjualan->biaya_admin > 0)
        <tr><td></td><td>Biaya Admin</td><td class="tr">− {{ number_format($penjualan->biaya_admin,0,',','.') }}</td></tr>
        @endif
        @if($penjualan->biaya_pengiriman > 0)
        <tr><td></td><td>Biaya Pengiriman</td><td class="tr">− {{ number_format($penjualan->biaya_pengiriman,0,',','.') }}</td></tr>
        @endif
        @if($penjualan->biaya_lainnya > 0)
        <tr><td></td><td>Biaya Lainnya</td><td class="tr">− {{ number_format($penjualan->biaya_lainnya,0,',','.') }}</td></tr>
        @endif
        <tr style="border-top:1px dashed #000;">
            <td></td><td><strong>Penghasilan Bersih</strong></td>
            <td class="tr"><strong>{{ number_format($penjualan->penghasilan_bersih,0,',','.') }}</strong></td>
        </tr>
        <tr><td></td><td>Modal Barang</td><td class="tr">− {{ number_format($penjualan->total_modal,0,',','.') }}</td></tr>
        <tr style="border-top:1px solid #000;">
            <td></td><td><strong>LABA BERSIH</strong></td>
            <td class="tr"><strong>{{ number_format($penjualan->laba_bersih,0,',','.') }}</strong></td>
        </tr>
    </table>

    @if($penjualan->catatan)
    <div class="line"></div>
    <div>Catatan: {{ $penjualan->catatan }}</div>
    @endif

    <div class="line"></div>
    <div class="tc" style="margin-top:6px; font-weight:bold;">TERIMA KASIH</div>
</div>

<script>window.onload = () => window.print(); window.onafterprint = () => window.close();</script>
</body>
</html>
