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
            {{-- <div class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Tanggal</label>
                    <div class="input-groupicon">
                        <input type="text" placeholder="DD-MM-YYYY" class="datetimepicker form-control" id="tanggal_penjualan">
                        <div class="addonset">
                            <img src="{{ asset('assets/img/icons/calendars.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Dari Gudang</label>
                    <select class="form-control select" id="gudang_from">
                        <option value="">Pilih</option>
                        <option>Store 1</option>
                        <option>Store 2</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Kepada</label>
                    <input type="text" class="form-control" id="nama_pelanggan" placeholder="Nama pelanggan">
                </div>
            </div> --}}

            <div class="col-lg-12 col-sm-6 col-12">
                <div class="form-group">
                    <label>Scan / Cari Barang</label>
                    <div class="input-groupicon">
                        <select id="barangSelect" style="width: 100%;" placeholder="Cari barang..."></select>

                    </div>
                    <div class="mt-2">
                        <button class="btn btn-info" id="btnScan">Toggle Scanner</button>
                         <div class="addonset">
                            <img src="{{ asset('assets/assets/img/icons/scanners.svg') }}" id="btnScan" alt="scan" style="cursor:pointer;">
                        </div>
                        </div>
                </div>
            </div>
        </div>

        <!-- Scanner -->
        <div id="reader" style="width:300px; display:none; margin-bottom: 10px;"></div>

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

    // Ketika pilih barang manual
    $('#barangSelect').on('select2:select', function (e) {
        const item = e.params.data;
        addBarangToTable(item);
        $('#barangSelect').val(null).trigger('change');
    });

    // ==================== SCANNER KAMERA ====================
    let html5QrCode;
    let isScanning = false;

    $('#btnScan').on('click', function () {
        const readerElem = document.getElementById('reader');
        if (!isScanning) {
            readerElem.style.display = 'block';
            html5QrCode = new Html5Qrcode("reader");
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        qrCodeMessage => {
                            html5QrCode.stop();
                            readerElem.style.display = 'none';
                            isScanning = false;
                            fetchBarangByCode(qrCodeMessage);
                        }
                    );
                    isScanning = true;
                }
            });
        } else {
            html5QrCode.stop();
            readerElem.style.display = 'none';
            isScanning = false;
        }
    });

    function fetchBarangByCode(kode) {
        fetch(`/api/barang/by-code/${kode}`)
            .then(res => res.json())
            .then(item => {
                if (item) {
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

    // ==================== TAMBAH BARANG KE TABEL ====================
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

    // ==================== UPDATE TOTAL PER BARIS ====================
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

    // ==================== GRAND TOTAL ====================
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('#barangTable tbody tr').each(function () {
            let totalText = $(this).find('.total').text().replace(/\D/g, '');
            let total = parseFloat(totalText) || 0;
            grandTotal += total;
        });
        $('#grandTotal').text(formatRupiah(grandTotal));
    }

    // ==================== FORMAT RUPIAH ====================
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

        const data = {
            tanggal: $('#tanggal_penjualan').val(),
            gudang_from: $('#gudang_from').val(),
            pelanggan: $('#nama_pelanggan').val(),
            items: items,
        };

        console.log("Data transaksi:", data);
        alert("Transaksi siap dikirim ke backend!");
        // Kirim ke backend pakai AJAX post
    });
});
</script>
@endpush
