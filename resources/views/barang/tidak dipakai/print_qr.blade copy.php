<!DOCTYPE html>
<html>

<head>
    <title>Cetak QR Barang</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 10mm;
    }

    h2 {
        text-align: center;
        margin-bottom: 15px;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 10px;
    }

    .item {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        display: inline-block;
        width: 180px;
        height: auto;
        vertical-align: top;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .item img {
        width: 120px;
        height: 120px;
    }

    .info {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.4;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 10mm;
        }

        /* ⛔ Hapus jeda otomatis antar item */
        .grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 8px;
        }

        .item {
            page-break-inside: avoid;
            break-inside: avoid;
            width: 180px;
        }
    }

    /* Pagination rapi */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin-top: 20px;
        gap: 6px;
    }

    .pagination li {
        display: inline-block;
    }

    .pagination li a,
    .pagination li span {
        display: inline-block;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: #f8f8f8;
        color: #333;
        text-decoration: none;
        font-size: 13px;
    }

    .pagination li.active span {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination li.disabled span {
        color: #aaa;
        background: #f2f2f2;
    }

    @media print {
        .pagination {
            display: none !important;
        }
    }
</style>

</head>

<body>
    <div class="no-print" style="margin-bottom:15px; text-align:center;">
        <button onclick="window.print()">🖨️ Print Sekarang</button>
    </div>
    <div class="grid">
        @foreach ($barangs as $barang)
            <div class="item">
                {!! QrCode::size(120)->generate($barang->id_barang) !!}
                <div class="info">
                    <strong>{{ $barang->nama_barang }}</strong><br>
                    <small>Kode: {{ $barang->kode_barang }}</small><br>
                    <small>Merk: {{ $barang->merk_barang ?? '-' }}</small><br>
                    <small>Lokasi: {{ $barang->lokasi ?? '-' }}</small>
                </div>
            </div>
        @endforeach
    </div>
    <div class="no-print text-center mt-4">
        <div class="d-inline-block">
            {{ $barangs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</body>

</html>
