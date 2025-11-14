<!DOCTYPE html>
<html>
<head>
    <title>Cetak QR Barang (Tom & Jerry)</title>
    <style>
        @page {
            size: A5 portrait; /* ubah ke A6 kalau kertasnya lebih kecil */
            margin: 10mm;
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
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
        }

        .item {
            border: 1px solid #ccc;
            padding: 5px;
            border-radius: 6px;
            text-align: center;
            page-break-inside: avoid;
        }

        .item svg {
            width: 80px;
            height: 80px;
        }

        .info {
            margin-top: 5px;
            font-size: 10px;
            line-height: 1.2;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }

        /* pagination disembunyikan saat print */
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

    <h2>QR Code Barang</h2>

    <div class="grid">
        @foreach ($barangs as $barang)
            <div class="item">
                {!! QrCode::size(80)->generate($barang->id_barang) !!}
                <img src="" alt="" srcset="">
                <div class="info">
                    <strong>{{ $barang->nama_barang ?? '-' }}</strong><br>
                    <small>Kode: {{ $barang->kode_barang ?? '-' }}</small><br>
                    <small>Merk: {{ $barang->merk_barang ?? '-' }}</small><br>
                    <small>ket: {{ $barang->keterangan ?? '-' }}</small><br>
                    <small>Harga:
    {{ $barang->harga_jual ? 'Rp ' . number_format($barang->harga_jual, 0, ',', '.') : '-' }}
</small> <br>
                    <small>KARISMA MOTOR BANGKALAN</small><br>
                </div>
            </div>
        @endforeach
    </div>

    <div class="no-print text-center" style="margin-top:10px;">
        {{ $barangs->links('pagination::bootstrap-5') }}
    </div>
</body>
</html>
