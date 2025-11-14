<!DOCTYPE html>
<html>
<head>
    <title>Cetak QR Barang (Stiker Sparepart)</title>
    <style>
        @page {
            size: 10cm 15cm portrait; /* Ukuran kertas Tom & Jerry */
            margin: 6mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 4mm;
            background: #fff;
        }

        h2 {
            text-align: center;
            font-size: 13px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 8px;
        }

        .item {
            display: flex;
            align-items: center;
            border: 1px solid #aaa;
            border-radius: 6px;
            padding: 6px 8px;
            background: #fafafa;
            page-break-inside: avoid;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }

        .qr {
            flex: 0 0 65px;
            text-align: center;
        }

        .qr svg {
            width: 50px;
            height: 50px;
        }

        .info {
            flex: 1;
            margin-left: 8px;
            font-size: 10px;
            line-height: 1.3;
        }

        .info strong {
            font-size: 11px;
            display: block;
            margin-bottom: 2px;
        }

        .harga {
            color: #0a6d1b;
            font-weight: bold;
            margin-top: 2px;
        }

        .brand {
            font-size: 9px;
            color: #333;
            text-align: right;
            margin-top: 3px;
            border-top: 1px dashed #ccc;
            padding-top: 2px;
            font-style: italic;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
                margin: 0;
            }
        }

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
                    {!! QrCode::size(65)->generate($barang->id_barang) !!}
                </div>
                <div class="info">
                    <strong>{{ strtoupper($barang->nama_barang ?? '-') }}</strong>
                    <small>Kode Sistem: {{ $barang->id_barang ?? '-' }}</small><br>
                    <small>Kode Part: {{ $barang->kode_barang ?? '-' }}</small><br>
                    <small>Merk: {{ $barang->merk_barang ?? '-' }}</small><br>
                    <small>Jenis Kendaraan: {{ $barang->jenis ?? '-' }}</small><br>
                    <small>Ket: {{ $barang->keterangan ?? '-' }}</small><br>
                    <small class="harga">
                        {{ $barang->harga_jual ? 'Rp ' . number_format($barang->harga_jual, 0, ',', '.') : '-' }}
                    </small>
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

<!--
    Catatan:
    - Pastikan package QR Code sudah terinstall: composer require simplesoftwareio/simple-qrcode
    - Pastikan di controller sudah mengirim data $barangs ke view ini.
    - Gunakan kertas label stiker yang sesuai ukuran (misal A5 atau A6).
    - Sesuaikan ukuran QR Code dan layout sesuai kebutuhan.
