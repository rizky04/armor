<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Service #{{ $service->nomor_service }}</title>

<style>
@media print {
    @page {
        size: 235mm 148mm; /* ukuran kertas continuous form */
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

/* garis --- */
.line {
    border-bottom: 1px dashed #000;
    margin: 2px 0;
}

/* header kiri dan kanan sejajar */
.header td {
    vertical-align: top;
    padding-bottom: 4px;
}

/* kolom tabel item */
.item-table td {
    padding: 2px 0;
}

/* total bagian kanan */
.total-right {
    width: 68%;
}

.ttd td {
    padding-top: 45px;
}
</style>

</head>
<body>

<div class="wrapper">
<!-- TITLE DI TENGAH ATAS -->
<div style="text-align:center; margin-bottom:10px; font-family:'Courier New', monospace;">
    <strong>INVOICE SERVICE</strong><br>
    No: {{ $service->nomor_service }}
</div>


<!-- HEADER MIRIP STRUK -->
<table class="header">
    <tr>
        <td style="width:50%;">
            Tanggal : {{ \Carbon\Carbon::parse($service->service_date)->format('d M Y') }}<br>
            Kepada : {{ $service->vehicle->client->nama_client ?? '-' }}<br>
            Plat  : {{ $service->vehicle->license_plate ?? '-' }}<br>
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
<table class="item-table">
    <tr style="border-bottom:1px dashed #000; font-weight:bold;">
        <td style="width:18%;">Kode Barang</td>
        <td style="width:40%;">Nama Barang</td>
        <td style="width:5%;">Qty</td>
        <td style="width:19%;">@Harga</td>
        {{-- <td style="width:6%; text-align:right;">Disc</td> --}}
        <td style="width:23%;">Total Harga</td>
    </tr>

    @php $totalSparepart = 0; @endphp
    @foreach($service->spareparts as $sp)
        @php $totalSparepart += $sp->subtotal; @endphp
        <tr>
            <td>{{ $sp->barang->kode_barang ?? '-' }}</td>
            <td>{{ $sp->barang->nama_barang ?? '' }}</td>
            <td>{{ $sp->qty }}</td>
            <td>{{ number_format($sp->barang->harga_jual ?? 0,0,',','.') }}</td>
            {{-- <td class="text-right">0</td> --}}
            <td>{{ number_format($sp->subtotal,0,',','.') }}</td>
        </tr>
    @endforeach

    @php $totalJasa = 0; @endphp
    @foreach($service->jobs as $job)
        @php $totalJasa += $job->subtotal; @endphp
        <tr>
            <td>-</td>
            <td>{{ $job->jasa->nama_jasa }}</td>
            <td>{{ $job->qty }}</td>
            <td>{{ number_format($job->jasa->harga_jasa ?? 0,0,',','.') }}</td>
            {{-- <td class="text-right">0</td> --}}
            <td>{{ number_format($job->subtotal,0,',','.') }}</td>
        </tr>
    @endforeach
</table>

<div class="line"></div>

<!-- TOTAL + SOSMED DALAM SATU TABEL (SEJAJAR) -->
@php $grandTotal = $totalSparepart + $totalJasa; @endphp

<table style="width:100%; font-family:'Courier New', monospace; line-height:1.2;">

    <tr>
        <!-- KOLOM KIRI: REKENING + SOSMED -->
        <td style="width:70%; vertical-align:top;">
            Rek BCA No: 185.1538.661 a/n Amarullah Rizki<br>
            Rek BRI No: 610701006387501 / a.n amarullah rizki <br>
            IG: @armor.garage | TikTok: @armor.garage
        </td>

        <!-- KOLOM KANAN: TOTAL -->
        <td style="width:15%;">Sub Total</td>
        <td style="width:15%; text-align:right;">
            {{ number_format($grandTotal,0,',','.') }}
        </td>
    </tr>

    <tr>
        <td></td>
        <td style="width:15%;"><b>Grand Total</b></td>
        <td style="width:15%; text-align:right;"><b>{{ number_format($grandTotal,0,',','.') }}</b></td>
    </tr>

</table>
 <div class="divider"></div>

    <strong>Mekanik:</strong><br>
    @forelse($service->mechanics ?? [] as $m)
        - {{ $m->name }}<br>
    @empty
        -
    @endforelse

    <div class="divider"></div>

<div class="line"></div>


</div>

<script>
window.onload = () => window.print();
window.onafterprint = () => window.close();
</script>

</body>
</html>
