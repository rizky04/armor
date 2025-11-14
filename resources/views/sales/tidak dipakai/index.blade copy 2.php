@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Transaksi Penjualan</h4>
        <h6>Tambah Data Penjualan Barang</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <!-- ==================== FORM INPUT ==================== -->
        <div class="row">
            <div class="col-lg-12 col-sm-6 col-12">
                <div class="form-group">
                    <label>Scan / Cari Barang</label>
                    <div class="input-groupicon">
                        <select id="barangSelect" style="width: 100%;" placeholder="Cari barang..."></select>
                    </div>

                    <div class="mt-3 text-center">
                        <button class="btn btn-info" id="btnToggleScanner">📷 Toggle Scanner</button>

                        <div class="mt-2" id="cameraControl" style="display:none;">
                            <label for="cameraSelect">Pilih Kamera:</label>
                            <select id="cameraSelect" class="form-select" style="max-width:300px; margin:auto;"></select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner -->
        <div id="reader" style="width:320px; display:none; margin:15px auto;"></div>

        <!-- ==================== TABEL BARANG ==================== -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="barangTable">
                <thead class="text-center">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Diskon (%)</th>
                        <th>Pajak (%)</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- ==================== GRAND TOTAL ==================== -->
        <div class="d-flex justify-content-end mt-3">
            <h5><strong>Grand Total: <span id="grandTotal">Rp 0</span></strong></h5>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary" id="btnSimpan">Simpan Transaksi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 & QR Scanner -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
$(document).ready(function () {
    // ==================== SELECT2 ====================
    $('#barangSelect').select2({
        placeholder: 'Cari atau scan barang...',
        ajax: {
            url: "{{ route('select2.barang') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id_barang,
                    text: `${item.kode_barang} - ${item.nama_barang}`,
                    nama: item.nama_barang,
                    kode: item.kode_barang,
                    harga: item.harga_jual,
                    stok: item.stock ?? 0
                }))
            })
        }
    });

    $('#barangSelect').on('select2:select', function (e) {
        const item = e.params.data;
        addBarangToTable(item);
        $('#barangSelect').val(null).trigger('change');
    });

    // ==================== SCANNER ====================
    let html5QrCode;
    let currentCameraId = null;
    let isScanning = false;

    $('#btnToggleScanner').on('click', async function () {
        const readerElem = document.getElementById('reader');
        const cameraSelect = document.getElementById('cameraSelect');
        const cameraControl = document.getElementById('cameraControl');

        if (!isScanning) {
            readerElem.style.display = 'block';
            cameraControl.style.display = 'block';
            html5QrCode = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = '';
                    devices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        option.text = device.label || `Kamera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    // pilih kamera belakang kalau ada
                    const backCam = devices.find(d => d.label.toLowerCase().includes('back'));
                    currentCameraId = backCam ? backCam.id : devices[0].id;
                    cameraSelect.value = currentCameraId;

                    startScanner(currentCameraId);
                    isScanning = true;
                } else {
                    alert("Kamera tidak terdeteksi di perangkat ini.");
                }
            }).catch(err => {
                alert("Izin kamera ditolak atau tidak tersedia. Aktifkan izin kamera di browser Anda.");
                console.error(err);
            });
        } else {
            stopScanner();
            readerElem.style.display = 'none';
            cameraControl.style.display = 'none';
        }
    });

    async function startScanner(cameraId) {
        try {
            await html5QrCode.start(
                { deviceId: { exact: cameraId } },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                qrCodeMessage => {
                    // Kamera tidak dimatikan, bisa scan terus
                    fetchBarangByCode(qrCodeMessage);
                },
                error => { /* scanning berjalan */ }
            );
        } catch (err) {
            console.error("Gagal mulai scanner:", err);
        }
    }

    async function stopScanner() {
        if (html5QrCode) {
            await html5QrCode.stop();
            await html5QrCode.clear();
            isScanning = false;
        }
    }

    $('#cameraSelect').on('change', async function() {
        await stopScanner();
        currentCameraId = this.value;
        startScanner(currentCameraId);
    });

    function fetchBarangByCode(kode) {
        fetch(`/api/barang/by-code/${kode}`)
            .then(res => res.json())
            .then(item => {
                if (item && item.id_barang) {
                    addBarangToTable({
                        id: item.id_barang,
                        nama: item.nama_barang,
                        kode: item.kode_barang,
                        harga: item.harga_jual,
                        stok: item.stock ?? 0
                    });
                } else {
                    alert('Barang tidak ditemukan!');
                }
            })
            .catch(err => console.error(err));
    }

    // ==================== TABEL BARANG ====================
    function addBarangToTable(item) {
        const tbody = $('#barangTable tbody');
        const exists = tbody.find(`tr[data-id="${item.id}"]`);

        if (exists.length) {
            let qtyInput = exists.find('.qty');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            updateTotal(exists);
            return;
        }

        let tr = `
            <tr data-id="${item.id}">
                <td>${item.nama}</td>
                <td><input type="number" value="1" class="form-control qty" style="width:70px;"></td>
                <td class="harga" data-harga="${item.harga}">${formatRupiah(item.harga)}</td>
                <td>${item.stok}</td>
                <td><input type="number" value="0" class="form-control diskon" style="width:70px;"></td>
                <td><input type="number" value="0" class="form-control pajak" style="width:70px;"></td>
                <td class="total">${formatRupiah(item.harga)}</td>
                <td><a href="#" class="btn btn-sm btn-danger delete-row">Hapus</a></td>
            </tr>
        `;
        tbody.append(tr);
        calculateGrandTotal();
    }

    $('#barangTable').on('input', '.qty, .diskon, .pajak', function() {
        const row = $(this).closest('tr');
        updateTotal(row);
    });

    $('#barangTable').on('click', '.delete-row', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    function updateTotal(row) {
        let harga = parseFloat(row.find('.harga').data('harga')) || 0;
        let qty = parseInt(row.find('.qty').val()) || 1;
        let diskon = parseFloat(row.find('.diskon').val()) || 0;
        let pajak = parseFloat(row.find('.pajak').val()) || 0;

        let subtotal = harga * qty;
        subtotal -= subtotal * (diskon / 100);
        subtotal += subtotal * (pajak / 100);

        row.find('.total').text(formatRupiah(subtotal));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('#barangTable tbody tr').each(function () {
            let totalText = $(this).find('.total').text().replace(/\D/g, '');
            let total = parseFloat(totalText) || 0;
            grandTotal += total;
        });
        $('#grandTotal').text(formatRupiah(grandTotal));
    }

    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // ==================== SIMPAN TRANSAKSI ====================
    $('#btnSimpan').on('click', function () {
        let items = [];
        $('#barangTable tbody tr').each(function () {
            items.push({
                id_barang: $(this).data('id'),
                qty: $(this).find('.qty').val(),
                harga: $(this).find('.harga').data('harga'),
                diskon: $(this).find('.diskon').val(),
                pajak: $(this).find('.pajak').val(),
            });
        });

        const data = { items };
        console.log("Data transaksi:", data);
        alert("Transaksi siap dikirim ke backend!");
    });
});
</script>
@endpush
