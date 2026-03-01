<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Service #{{ $service->nomor_service }}</title>

<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
    }

    body {
        font-family: "Courier New", monospace;
        font-size: 12px;
        line-height: 1.25;
    }

    /* width A4 = 210mm - margin 5mm kiri/kanan */
    .wrapper {
        width: 200mm;
        margin: auto;
    }

    table { width: 100%; border-collapse: collapse; }

    .line {
        border-bottom: 1px dashed #000;
        margin: 4px 0;
    }

    .header td {
        vertical-align: top;
        padding-bottom: 4px;
    }

    .item-table td {
        padding: 2px 0;
    }

    .ttd td {
        padding-top: 45px;
    }
</style>
</head>

<body>

<div class="wrapper">

    <div style="text-align:center; margin-bottom:10px;">
        <strong>INVOICE SERVICE</strong><br>
        No: {{ $service->nomor_service }}
    </div>

    <table class="header">
        <tr>
            <td style="width:50%;">
                Tanggal : {{ \Carbon\Carbon::parse($service->service_date)->format('d M Y') }}<br>
                Kepada : {{ $service->vehicle->client->nama_client ?? $service->manual_customer_name ?? '-' }}<br>
                type : {{ $service->manual_vehicle_name ?? '-' }}<br>
                Plat  : {{ $service->vehicle->license_plate ?? $service->manual_license_plate ?? '-' }}<br>
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

    <table class="item-table">
        <tr style="border-bottom:1px dashed #000; font-weight:bold;">
            <td style="width:18%;">Kode Barang</td>
            <td style="width:40%;">Nama Barang</td>
            <td style="width:5%;">Qty</td>
            <td style="width:19%;">@Harga</td>
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
                <td>{{ number_format($job->subtotal,0,',','.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    @php $grandTotal = $totalSparepart + $totalJasa; @endphp

    <table style="width:100%; line-height:1.2;">
        <tr>
            <!-- Info Bank -->
            <td style="width:70%; vertical-align:top;">
                Rek BCA No: 185.1538.661 a/n Amarullah Rizki<br>
                Rek BRI No: 610701006387501 / a.n Amarullah Rizki <br>
                IG: @armor.garage | TikTok: @armor.garage
            </td>

            <!-- Total -->
            <td style="width:15%;">Sub Total</td>
            <td style="width:15%; text-align:right;">
                {{ number_format($grandTotal,0,',','.') }}
            </td>
        </tr>

        <tr>
            <td></td>
            <td><b>Grand Total</b></td>
            <td style="text-align:right;"><b>{{ number_format($grandTotal,0,',','.') }}</b></td>
        </tr>
    </table>

    <div class="line"></div>

    <strong>Mekanik:</strong><br>
    @forelse($service->mechanics ?? [] as $m)
        - {{ $m->name }}<br>
    @empty
        -
    @endforelse

    <div class="line"></div>

</div>

<script>
    window.onload = () => window.print();
    window.onafterprint = () => window.close();
</script>

</body>
</html>
