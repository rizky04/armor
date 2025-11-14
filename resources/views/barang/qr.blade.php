<!DOCTYPE html>
<html>
<head>
    <title>Label QR Sparepart</title>
    <style>
        @page {
            size: auto;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }

        .label {
            border: 1.4px solid #000;
            border-radius: 5px;
            padding: 3mm;
            margin: 0 auto 2mm auto;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            justify-content: space-between;
            background: #fff;
            width: 72mm;
            page-break-inside: avoid;
        }

        .info {
            flex: 1;
            padding-right: 3mm;
            border-right: 1px dashed #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .nama {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1mm;
            line-height: 1.2;
        }

        .partno {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            margin-bottom: 1mm;
            letter-spacing: 0.3px;
            width: 100%;
        }

        .detail {
            font-size: 11px;
            line-height: 1.4;
            text-align: left;
            width: 100%;
            font-weight: bold; /* semua detail jadi tebal */
        }

        .harga {
            font-size: 16px;
            font-weight: bold;
            margin-top: 2mm;
            width: 100%;
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 1mm;
        }

        .footer {
            text-align: center;
            font-size: 10px; /* diperkecil */
            font-weight: bold;
            letter-spacing: 0.3px;
            margin-top: 1mm;
            width: 100%;
            white-space: nowrap; /* agar tetap satu baris */
        }

        .qrbox {
            text-align: center;
            width: 75px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .qrbox .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70px;
            margin-bottom: 3mm;
            overflow: hidden;
        }

        .qrbox .logo img {
            width: 75px;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .qrbox .qr svg {
            width: 65px;
            height: 65px;
        }

        .no-print {
            text-align: center;
            margin: 5mm 0;
        }

        button {
            background: #000;
            color: #fff;
            border: none;
            padding: 7px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
        }

        button:hover {
            background: #333;
        }

        @media print {
            .no-print { display: none; }
            body, html {
                background: #fff;
                margin: 0;
                padding: 0;
                width: 80mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <label>Jumlah Cetak:</label>
    <input type="number" id="jumlahCetak" value="1" min="1" style="width:60px; text-align:center;">
    <button onclick="window.print()">🖨️ Cetak Label</button>
</div>

<div id="labelContainer"></div>

<!-- Template disembunyikan -->
<div class="label" id="labelTemplate" style="display:none">
    <div class="info">
        <div class="nama">{{ strtoupper($barang->nama_barang ?? '-') }}</div>
        <div class="partno">PART NO: {{ $barang->kode_barang ?? '-' }}</div>
        <div class="detail">Kode Sistem: {{ $barang->id_barang ?? '-' }}</div>
        <div class="detail">Merk: {{ $barang->merk_barang ?? '-' }}</div>
        <div class="detail">Jenis: {{ $barang->jenis ?? '-' }}</div>
        <div class="detail">Keterangan: {{ $barang->keterangan ?? '-' }}</div>
        <div class="harga">Rp {{ $barang->harga_jual ? number_format($barang->harga_jual, 0, ',', '.') : '-' }}</div>
        <div class="footer">ARMOR MOTOR BANGKALAN</div>
    </div>

    <div class="qrbox">
        <div class="logo">
            <img src="{{ asset('assets/assets/img/barcode1234.png') }}" alt="Logo Toko">
        </div>
        <div class="qr">
            {!! QrCode::size(65)->generate($barang->id_barang) !!}
        </div>
    </div>
</div>

<script>
const jumlahInput = document.getElementById('jumlahCetak');
const container = document.getElementById('labelContainer');
const template = document.getElementById('labelTemplate').outerHTML;

function updateLabels() {
    const jumlah = parseInt(jumlahInput.value) || 1;
    container.innerHTML = ''; // kosongkan kontainer

    for (let i = 0; i < jumlah; i++) {
        const newLabel = template.replace('style="display:none"', ''); // hilangkan display:none
        container.innerHTML += newLabel;
    }
}

// Event langsung saat input berubah
jumlahInput.addEventListener('input', updateLabels);

// render awal
updateLabels();
</script>

</body>
</html>
