<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota {{ $transaksi->nomor_transaksi }}</title>
<style>
    @media print {
        @page { size: A4 portrait; margin: 5mm; }
        .no-print { display: none; }
    }

    body {
        font-family: "Courier New", monospace;
        font-size: 12px;
        line-height: 1.3;
    }

    .wrapper { width: 200mm; margin: auto; }

    table { width: 100%; border-collapse: collapse; }

    .line { border-bottom: 1px dashed #000; margin: 5px 0; }

    .item-table td { padding: 2px 0; vertical-align: top; }

    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .no-print {
        text-align: center;
        margin-bottom: 12px;
    }
    .no-print button {
        padding: 8px 20px;
        font-size: 14px;
        cursor: pointer;
    }
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨 Print Nota</button>
    <button onclick="window.close()" style="margin-left:8px;">✕ Tutup</button>
</div>

<div class="wrapper">

    <!-- HEADER BENGKEL -->
    <div class="text-center" style="margin-bottom:8px;">
        <strong style="font-size:15px;">ARMOR GARAGE - SHOWROOM & SERVICE</strong><br>
        Jl. Raya Bancaran, Bangkalan, Jawa Timur<br>
        0895-2354-1922 | IG: @armor.garage
    </div>

    <div class="line"></div>

    <!-- INFO TRANSAKSI -->
    <table>
        <tr>
            <td style="width:50%;">
                No : {{ $transaksi->nomor_transaksi }}<br>
                Tgl: {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
            </td>
            <td style="width:50%; text-align:right;">
                Customer : {{ $transaksi->nama_customer }}<br>
                HP       : {{ $transaksi->no_hp ?? '-' }}<br>
                Motor    : {{ $transaksi->plat_nomor ?? '-' }} {{ $transaksi->nama_motor ? '- '.$transaksi->nama_motor : '' }}
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- BARANG -->
    @if($transaksi->barangs->count())
    <div style="font-weight:bold; margin-bottom:3px;">BARANG / SPAREPART</div>
    <table class="item-table">
        <tr style="border-bottom:1px dashed #000; font-weight:bold;">
            <td style="width:55%;">Nama Barang</td>
            <td style="width:8%; text-align:center;">Qty</td>
            <td style="width:17%; text-align:right;">Harga</td>
            <td style="width:20%; text-align:right;">Subtotal</td>
        </tr>
        @foreach($transaksi->barangs as $item)
        <tr>
            <td>{{ $item->nama_barang }}</td>
            <td class="text-center">{{ $item->qty }}</td>
            <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr style="border-top:1px dashed #000;">
            <td colspan="3" class="text-right"><strong>Total Barang</strong></td>
            <td class="text-right"><strong>{{ number_format($transaksi->total_barang, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="line"></div>
    @endif

    <!-- JASA -->
    @if($transaksi->jasas->count())
    <div style="font-weight:bold; margin-bottom:3px;">JASA / PEKERJAAN</div>
    <table class="item-table">
        <tr style="border-bottom:1px dashed #000; font-weight:bold;">
            <td style="width:60%;">Nama Jasa</td>
            <td style="width:8%; text-align:center;">Qty</td>
            <td style="width:32%; text-align:right;">Subtotal</td>
        </tr>
        @foreach($transaksi->jasas as $item)
        <tr>
            <td>{{ $item->nama_jasa }}</td>
            <td class="text-center">{{ $item->qty }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr style="border-top:1px dashed #000;">
            <td colspan="2" class="text-right"><strong>Total Jasa</strong></td>
            <td class="text-right"><strong>{{ number_format($transaksi->total_jasa, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="line"></div>
    @endif

    <!-- GRAND TOTAL -->
    <table>
        <tr>
            <td style="width:60%; vertical-align:top; font-size:11px;">
                Rek BCA  : 185.1538.661 a/n Amarullah Rizki<br>
                Rek BRI  : 610701006387501 a/n Amarullah Rizki<br>
                TikTok   : @armor.garage
            </td>
            <td style="width:40%; text-align:right;">
                <table style="width:100%;">
                    <tr>
                        <td>Total Barang</td>
                        <td class="text-right">{{ number_format($transaksi->total_barang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Jasa</td>
                        <td class="text-right">{{ number_format($transaksi->total_jasa, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top:1px solid #000;">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format($transaksi->total, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-size:11px; padding-top:3px;">Bayar via</td>
                        <td class="text-right" style="font-size:11px; padding-top:3px;">
                            <strong>{{ strtoupper($transaksi->metode_pembayaran ?? 'CASH') }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="line"></div>

    @if($transaksi->catatan)
    <div style="font-size:11px; margin-bottom:4px;">Catatan: {{ $transaksi->catatan }}</div>
    <div class="line"></div>
    @endif

    <div class="text-center" style="margin-top:6px; font-weight:bold;">
        TERIMA KASIH ATAS KEPERCAYAAN ANDA!
    </div>

</div>

<script>
    window.onload = () => window.print();
    window.onafterprint = () => window.close();
</script>
</body>
</html>
