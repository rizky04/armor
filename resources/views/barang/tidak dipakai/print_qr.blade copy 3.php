<!DOCTYPE html>
<html>
<head>
    <title>Cetak QR Barang (Stiker Sparepart)</title>
    <style>
        @page {
            size: A5 portrait; /* ganti ke A6 kalau kertasnya kecil */
            margin: 8mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5mm;
        }

        h2 {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 10px;
        }

        .item {
            display: flex;
            align-items: center;
            border: 1px solid #bbb;
            border-radius: 8px;
            padding: 6px 8px;
            background: #fff;
            page-break-inside: avoid;
            box-shadow: 0 0 2px rgba(0,0,0,0.1);
        }

        .qr {
            flex: 0 0 70px;
            text-align: center;
        }

        .qr svg {
            width: 70px;
            height: 70px;
        }

        .info {
            flex: 1;
            margin-left: 8px;
            font-size: 11px;
            line-height: 1.3;
        }

        .info strong {
            font-size: 12px;
        }

        .harga {
            color: #0a6d1b;
            font-weight: bold;
        }

        .brand {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
            border-top: 1px dashed #ccc;
            padding-top: 2px;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                background: white;
            }
        }

        /* Pagination (disembunyikan saat print) */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 4px;
            padding: 0;
            margin-top: 10px;
        }

        .pagination li a,
        .pagination li span {
            font-size: 10px;
            padding: 3px 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
            text-decoration: none;
            color: #333;
        }

        .pagination li.active span {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        @media print {
            .pagination {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:10px; text-align:center;">
        <button onclick="window.print()">🖨️ Print Sekarang</button>
    </div>

    <h2>STIKER QR BARANG SPAREPART</h2>

    <div class="grid">
        @foreach ($barangs as $barang)
            <div class="item">
                <div class="qr">
                    {!! QrCode::size(70)->generate($barang->id_barang) !!}
                </div>
                <div class="info">
                    <strong>{{ strtoupper($barang->nama_barang ?? '-') }}</strong><br>
                    <small>Kode sistem: {{ $barang->id_barang ?? '-' }}</small><br>
                    <small>Kode Part: {{ $barang->kode_barang ?? '-' }}</small><br>
                    <small>Merk: {{ $barang->merk_barang ?? '-' }}</small><br>
                    <small>Jenis Mobil: {{ $barang->jenis ?? '-' }}</small><br>
                    <small>Ket: {{ $barang->keterangan ?? '-' }}</small><br>
                    <small class="harga">
                        Harga: {{ $barang->harga_jual ? 'Rp ' . number_format($barang->harga_jual, 0, ',', '.') : '-' }}
                    </small><br>
                    <div class="brand">ARMOR MOTOR BANGKALAN</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="no-print text-center" style="margin-top:10px;">
        {{ $barangs->links('pagination::bootstrap-5') }}
    </div>
</body>
</html>
