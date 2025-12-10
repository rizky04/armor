<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Service #{{ $service->nomor_service }}</title>

<style>
    @media print {
        @page {
            size: A4;
            margin: 5mm;
        }
    }

    body {
        font-family: "Courier New", Courier, monospace;
        font-size: 12px; /* ukuran khas dotmatrix */
        line-height: 1.3;
        font-weight: normal;
    }

    .wrapper {
        width: 235mm; /* ukuran fanfold dotmatrix */
        margin: auto;
        padding: 5px;
        border: none;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    .divider {
        border-bottom: 1px dashed #000;
        margin: 2px 0;
    }

    table { width: 100%; }
    table td { vertical-align: top; }

    .border-top {
        border-top: 1px dashed black;
        padding-top: 3px;
    }
</style>
</head>

<body>
<div class="wrapper">

    <div class="text-center">
        <strong>ARMOR MOTOR</strong><br>
        Jl. Raya Bancaran<br>
        -------------------------------------------<br>
        <strong>INVOICE SERVICE</strong><br>
        No: {{ $service->nomor_service }}<br>
        Tgl: {{ \Carbon\Carbon::parse($service->service_date)->format('d/m/Y H:i') }}<br>
        -------------------------------------------
    </div>

    Nama  : {{ $service->vehicle->client->nama_client ?? '-' }}<br>
    Telp  : {{ $service->vehicle->client->no_telp ?? '-' }}<br>
    Plat  : {{ $service->vehicle->license_plate ?? '-' }}<br>
    Status Servis : {{ ucfirst($service->status ?? '-') }}<br>
    Status Bayar  : {{ $service->status_bayar ?? '-' }}<br>

    <div class="divider"></div>

    <strong>Pekerjaan</strong><br>
    <table>
        <tr><td>Jasa</td><td>Qty</td><td class="text-right">Subtotal</td></tr>
        @php $totalJasa = 0; @endphp
        @foreach($service->jobs ?? [] as $job)
            @php $totalJasa += $job->subtotal; @endphp
            <tr>
                <td>{{ $job->jasa->nama_jasa ?? '-' }}</td>
                <td>{{ $job->qty }}</td>
                <td class="text-right">{{ number_format($job->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <strong>Sparepart</strong><br>
    <table>
        <tr><td>Barang</td><td>Qty</td><td class="text-right">Subtotal</td></tr>
        @php $totalSparepart = 0; @endphp
        @foreach($service->spareparts ?? [] as $sp)
            @php $totalSparepart += $sp->subtotal; @endphp
            <tr>
                <td>{{ $sp->barang->nama_barang ?? '-' }} {{ $sp->barang->merk_barang ?? '' }}</td>
                <td>{{ $sp->qty }}</td>
                <td class="text-right">{{ number_format($sp->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    @php $grandTotal = $totalJasa + $totalSparepart; @endphp

    <table>
        <tr><td>Total Jasa</td><td class="text-right">{{ number_format($totalJasa, 0, ',', '.') }}</td></tr>
        <tr><td>Total Sparepart</td><td class="text-right">{{ number_format($totalSparepart, 0, ',', '.') }}</td></tr>
        <tr class="border-top">
            <td>Grand Total</td><td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <tr><td>Dibayar</td><td class="text-right">{{ number_format($service->total_paid, 0, ',', '.') }}</td></tr>
        <tr><td>Sisa Bayar</td><td class="text-right">{{ number_format($service->due_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Kembali</td><td class="text-right">
            {{ number_format($service->payments->last()->change_amount ?? 0, 0, ',', '.') }}
        </td></tr>
    </table>

    <div class="divider"></div>

    <strong>Mekanik:</strong><br>
    @forelse($service->mechanics ?? [] as $m)
        - {{ $m->name }}<br>
    @empty
        -
    @endforelse

    <div class="divider"></div>

    <div class="text-center">
        *** TERIMA KASIH ***
    </div>

</div>

<script>
window.onload = () => {
    window.print();
};
window.onafterprint = () => {
    window.close();
};
</script>

</body>
</html>
