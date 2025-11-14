<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Service #{{ $service->nomor_service }}</title>
    <style>
    /* ================================
       STYLING KHUSUS PRINTER TERMAL
       ================================ */
    @media print {
        @page {
            size: 80mm auto; /* Bisa diganti ke 58mm kalau printer kecil */
            margin: 0;
        }
        body {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: bold;
        }
        .no-print {
            display: none;
        }
    }

    body {
        background: #fff;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        line-height: 1.3;
        font-weight: bold;
    }

    .invoice-wrapper {
        width: 80mm;
        margin: 10px auto;
        padding: 10px;
        border: 1px solid #000;
        background: #fff;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .dotted { border-bottom: 1px dotted #000; margin: 5px 0; }
    hr.dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }
    .total-line { border-top: 1px solid #000; margin-top: 5px; padding-top: 5px; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 3px 0; font-weight: bold; }
    .mt-2 { margin-top: 6px; }
    .mb-1 { margin-bottom: 4px; }
    </style>
</head>
<body>

<div class="invoice-wrapper">

    <!-- HEADER -->
    <div class="text-center">
        <h5>KARISMA MOTOR</h5>
        <p>Jl. Raya Nyorondung No. 96, Pamorah, Bangkalan</p>
        <p>Telp: 0878 - 4513 - 3640</p>
        <hr class="dashed">
        <p>INVOICE SERVICE</p>
        <p>No: {{ $service->nomor_service }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($service->service_date)->format('d/m/Y H:i') }}</p>
        <hr class="dashed">
    </div>

    <!-- CUSTOMER & VEHICLE INFO -->
    <div>
        <p>Nama: {{ $service->vehicle->client->nama_client ?? '-' }}</p>
        <p>Telp: {{ $service->vehicle->client->no_telp ?? '-' }}</p>
        <p>Plat: {{ $service->vehicle->license_plate ?? '-' }}</p>
        <p>Status: {{ ucfirst($service->status ?? '-') }}</p>
        <p>Status bayar: {{ $service->status_bayar ?? '-' }}</p>
        <hr class="dotted">
    </div>

    <!-- PEKERJAAN -->
    <p>Pekerjaan:</p>
    <table class="table">
        <thead>
            <tr>
                <th>Jasa</th>
                <th>qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalJasa = 0; @endphp
            @forelse($service->jobs as $job)
                @php $totalJasa += $job->subtotal; @endphp
                <tr>
                    <td>{{ $job->jasa->nama_jasa ?? '-' }}</td>
                    <td>{{ $job->qty ?? '-' }}</td>
                    <td class="text-right">{{ number_format($job->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- SPAREPART -->
    <p class="mt-2">Sparepart:</p>
    <table class="table">
        <thead>
            <tr>
                <th>Barang</th>
                <th>qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSparepart = 0; @endphp
            @forelse($service->spareparts as $sp)
                @php $totalSparepart += $sp->subtotal; @endphp
                <tr>
                    <td>{{ $sp->barang->nama_barang ?? '-' }} {{ $sp->barang->merk_barang ?? '-' }} {{ $sp->barang->jenis ?? '-' }} {{ $sp->barang->keterangan ?? '-' }}</td>
                     <td>{{ $sp->qty }}</td>
                    <td class="text-right">{{ number_format($sp->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTAL -->
    @php $grandTotal = $totalJasa + $totalSparepart; @endphp
    <hr class="dashed">
    <table class="table">
        <tr>
            <td>Total Jasa</td>
            <td class="text-right">Rp {{ number_format($totalJasa, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Sparepart</td>
            <td class="text-right">Rp {{ number_format($totalSparepart, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-line">
            <td>Grand Total</td>
            <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Dibayar</td>
            <td class="text-right">Rp {{ number_format($service->total_paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Sisa bayar</td>
            <td class="text-right">Rp {{ number_format($service->due_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">Rp {{ number_format($service->payments->last()->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr class="dashed">

    <!-- MEKANIK -->
    <p>Mekanik:</p>
    <ul style="margin:0; padding-left:15px;">
        @forelse($service->mechanics as $m)
            <li>{{ $m->name }}</li>
        @empty
            <li>-</li>
        @endforelse
    </ul>

    <hr class="dotted">
    <p class="text-center">Terima kasih atas kepercayaan Anda!</p>
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
