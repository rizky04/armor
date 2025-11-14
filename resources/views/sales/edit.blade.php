@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Sales</h4>
        <h6>Edit Data Sales</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <!-- ==================== FORM INPUT ==================== -->
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" class="form-control" id="sales_date" value="{{ $sales->sales_date }}">
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="form-group">
                    <label>Client</label>
                    <select id="id_client" class="form-control">
                        <option value="{{ $sales->id_client }}">{{ $sales->client->nama_client }}</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="form-group">
                    <label>Jatuh Tempo</label>
                    <input type="date" class="form-control" id="due_date" value="{{ $sales->due_date }}">
                </div>
            </div>

            <div class="col-lg-11 col-sm-10 col-10">
                <div class="form-group">
                    <label>Scan / Cari Barang</label>
                    <div class="input-groupicon">
                        <select id="barangSelect" style="width: 100%;" placeholder="Cari barang..."></select>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-sm-2 col-2">
                <div class="form-group">
                    <label>QR</label>
                    <button class="btn btn-info" id="btnToggleScanner">📷</button>
                </div>
            </div>

             <div class="col-lg-12 col-sm-12 col-12">
                    <div class="form-group">
                        <label>Scan Barcode Barang</label>
                        <input type="text" id="scanBarcode" class="form-control" placeholder="Arahkan scanner ke sini..."
                            autofocus>
                    </div>
                </div>
        </div>

        <div class="mt-3 text-center">
            <div class="mt-2" id="cameraControl" style="display:none;">
                <label for="cameraSelect">Pilih Kamera:</label>
                <select id="cameraSelect" class="form-select" style="max-width:300px; margin:auto;"></select>
            </div>
        </div>

        <div id="reader" style="width:320px; display:none; margin:15px auto;"></div>

        <!-- ==================== TABEL BARANG ==================== -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered text-center" id="barangTable">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales->salesItems as $item)
                    <tr data-id="{{ $item->barang->id_barang }}">
                        <td>{{ $item->barang->id_barang }} -  {{ $item->barang->kode_barang }} - {{ $item->barang->nama_barang }} - {{ $item->barang->merk_barang }} - {{ $item->barang->jenis }} - {{ $item->barang->keterangan }}
                            <input type="hidden" class="harga-kulak" value="{{ $item->barang->harga_kulak }}">
                        </td>
                        <td><input type="number" class="form-control qty text-center" value="{{ $item->qty }}" style="width:70px;"></td>
                        <td class="harga" data-harga="{{ $item->price }}">{{ 'Rp '.number_format($item->price,0,',','.') }}</td>
                        <td>{{ $item->barang->stok_barang }}</td>
                        <td class="total">{{ 'Rp '.number_format($item->subtotal,0,',','.') }}</td>
                        <td><a href="#" class="btn btn-sm btn-danger delete-row"><i class="fa-solid fa-trash"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ==================== GRAND TOTAL ==================== -->
        <div class="d-flex justify-content-end mt-3">
            <h5><strong>Grand Total: <span id="grandTotal">Rp {{ number_format($sales->total,0,',','.') }}</span></strong></h5>
        </div>

        <div class="row mt-3">
    <div class="col-lg-4">
        <div class="form-group">
            <label>Status Pembayaran</label>
            <select id="status_bayar" class="form-control">
                <option value="belum bayar" {{ $sales->status_bayar == 'belum bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="hutang" {{ $sales->status_bayar == 'hutang' ? 'selected' : '' }}>Hutang</option>
                <option value="lunas" {{ $sales->status_bayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
    </div>

    <div class="col-lg-4" id="due_date_div" style="display: {{ $sales->status_bayar == 'hutang' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Jatuh Tempo</label>
            <input type="date" id="due_date" class="form-control" value="{{ $sales->due_date }}">
        </div>
    </div>

    <div class="col-lg-4 payment-field" style="display: {{ $sales->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select id="payment_type" class="form-control">
                <option value="">-- Pilih Metode --</option>
                <option value="cash" {{ $sales->payments->first()?->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="transfer" {{ $sales->payments->first()?->payment_type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="qris" {{ $sales->payments->first()?->payment_type == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>
    </div>

    <div class="col-lg-6 payment-field" style="display: {{ $sales->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Jumlah Bayar</label>
            <input type="text" id="amount_paid" class="form-control text-end"
                value="{{ number_format($sales->payments->first()?->amount_paid ,0,',','.') ?? '' }}">
        </div>
    </div>

    <div class="col-lg-6 payment-field" style="display: {{ $sales->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Kembalian</label>
            <input type="text" id="change_amount" class="form-control text-end bg-light"
                value="{{ $sales->payments->first()?->change_amount ?? 0 }}" readonly>
        </div>
    </div>
</div>


        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Catatan / Keterangan</label>
                    <textarea id="note" class="form-control" rows="2">{{ $sales->note }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary" id="btnUpdate">Update Transaksi</button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<audio id="beepSound" src="https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg"></audio>

<script>
$(document).ready(function() {

    $('#status_bayar').on('change', function() {
    const status = $(this).val();

    if (status === 'hutang') {
        $('#due_date_div').show();
        $('.payment-field').hide();
    } else if (status === 'lunas') {
        $('#due_date_div').hide();
        $('.payment-field').show();
    } else {
        $('#due_date_div').hide();
        $('.payment-field').hide();
    }
});



     // ==================== SCAN BARCODE INPUT ====================
            $('#scanBarcode').on('keypress', function(e) {
                if (e.which === 13) { // tekan Enter otomatis dari scanner
                    e.preventDefault();
                    const kode = $(this).val().trim();
                    if (kode !== '') {
                        fetchBarangByQR(kode);
                        $(this).val(''); // kosongkan input setelah scan
                    }
                }
            });

            async function fetchBarangByQR(kode) {
                try {
                    const res = await fetch(`/api/barang/by-qr/${kode}`);
                    if (!res.ok) throw new Error('Barang tidak ditemukan');

                    const item = await res.json();
                    addBarangToTable({
                        id: item.id_barang,
                        nama: item.nama_barang,
                        kode: item.kode_barang,
                        harga: item.harga_jual,
                        harga_kulak: item.harga_kulak,
                        stok: item.stok_barang ?? 0,
                        merk: item.merk_barang,
                        keterangan: item.keterangan,
                        jenis: item.jenis,
                    });

                    // Bunyi beep atau animasi sukses
                    const beepSound = document.getElementById("beepSound");
                    beepSound.play();

                } catch (err) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang tidak ditemukan!',
                        text: `Kode: ${kode}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }



document.addEventListener('click', function enableBeepOnce() {
    const beepSound = document.getElementById("beepSound");
    beepSound.play().then(() => {
        beepSound.pause();
        beepSound.currentTime = 0;
    }).catch(err => {
        console.log("Menunggu izin audio dari browser...");
    });
    document.removeEventListener('click', enableBeepOnce);
});

    // ==================== SELECT2 BARANG ====================
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
                    text: `${item.kode_barang} - ${item.nama_barang} (Stok: ${item.stok_barang ?? 0}) - Harga: Rp ${item.harga_jual.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`,
                    nama: item.nama_barang,
                    harga: item.harga_jual,
                    harga_kulak: item.harga_kulak,
                    stok: item.stok_barang ?? 0,
                    merk: item.merk_barang,
                        keterangan: item.keterangan,
                        jenis: item.jenis,
                }))
            })
        }
    }).on('select2:select', function(e) {
        const item = e.params.data;
        addBarangToTable(item);
        $('#barangSelect').val(null).trigger('change');
    });

    // ==================== FUNGSI BANTUAN ====================
    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateTotal(row) {
        let harga = parseFloat(row.find('.harga').data('harga')) || 0;
        let qty = parseInt(row.find('.qty').val()) || 1;
        let subtotal = harga * qty;
        row.find('.total').text(formatRupiah(subtotal));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        $('#barangTable tbody tr').each(function() {
            let val = $(this).find('.total').text().replace(/\D/g,'');
            total += parseInt(val) || 0;
        });
        $('#grandTotal').text(formatRupiah(total));
        syncAmountPaid();
    }

    $('#barangTable').on('input', '.qty', function() {
        const row = $(this).closest('tr');
        updateTotal(row);
    });

    $('#barangTable').on('click', '.delete-row', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    function addBarangToTable(item) {
        const tbody = $('#barangTable tbody');
        const exists = tbody.find(`tr[data-id="${item.id_barang || item.id}"]`);
        if (exists.length) {
            let qty = exists.find('.qty');
            qty.val(parseInt(qty.val()) + 1);
            updateTotal(exists);
            return;
        }
        let tr = `
        <tr data-id="${item.id_barang || item.id}">
            <td>${item.id || item.id_barang} - ${item.kode || item.kode_barang} - ${item.nama_barang || item.nama} - ${item.merk || item.merk_barang} - ${item.jenis || item.jenis} - ${item.keterangan} <input type="hidden" class="harga-kulak" value="${item.harga_kulak}"></td>
            <td><input type="number" class="form-control qty text-center" value="1" style="width:70px;"></td>
            <td class="harga" data-harga="${item.harga_jual || item.harga}">${formatRupiah(item.harga_jual || item.harga)}</td>
            <td>${item.stock || item.stok}</td>
            <td class="total">${formatRupiah(item.harga_jual || item.harga)}</td>
            <td><a href="#" class="btn btn-sm btn-danger delete-row"><i class="fa-solid fa-trash"></i></a></td>
        </tr>`;
        tbody.append(tr);
        calculateGrandTotal();
    }

    // ==================== UPDATE TRANSAKSI ====================
    $('#btnUpdate').on('click', function() {
        let items = [];
        $('#barangTable tbody tr').each(function() {
            items.push({
                id_barang: $(this).data('id'),
                qty: $(this).find('.qty').val(),
                harga: $(this).find('.harga').data('harga'),
                purchase_price: $(this).find('.harga-kulak').val()
            });
        });

        const data = {
            sales_date: $('#sales_date').val(),
            id_client: $('#id_client').val(),
            due_date: $('#due_date').val(),
            note: $('#note').val(),
            amount_paid: parseRupiahToNumber($('#amount_paid').val()),
            change_amount: parseRupiahToNumber($('#change_amount').val()),
            status_bayar: $('#status_bayar').val(),
            payment_type: $('#payment_type').val(),
            items
        };

        $.ajax({
            url: "{{ route('sales.update', $sales->id) }}",
            type: "POST", // pakai POST agar tidak error 405
            data: data,
            success: res => {
                Swal.fire('Berhasil', 'Data sales berhasil diperbarui!', 'success')
                .then(() => window.location.href = "{{ route('sales.index') }}");
            },
            error: err => {
                Swal.fire('Gagal', 'Terjadi kesalahan saat update data', 'error');
            }
        });
    });

    // ==================== SELECT2 CLIENT ====================
    $('#id_client').select2({
        placeholder: 'Pilih Client...',
        allowClear: true,
        ajax: {
            url: "{{ route('select2.clients') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(c => ({ id: c.id_client, text: c.nama_client }))
            })
        }
    });

    // ==================== QR SCANNER ====================
    const html5QrCode = new Html5Qrcode("reader");
    let cameraId = null;

    $("#btnToggleScanner").on("click", async function() {
        if ($("#reader").is(":visible")) {
            html5QrCode.stop();
            $("#reader, #cameraControl").hide();
        } else {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                $("#cameraSelect").empty();
                devices.forEach(cam => {
                    $("#cameraSelect").append(`<option value="${cam.id}">${cam.label}</option>`);
                });
                cameraId = devices[0].id;
                $("#reader, #cameraControl").show();
                startScanner();
            } else {
                alert("Kamera tidak ditemukan!");
            }
        }
    });

    $("#cameraSelect").on("change", function() {
        cameraId = $(this).val();
        html5QrCode.stop().then(startScanner);
    });

    function startScanner() {
        html5QrCode.start(
            cameraId,
            { fps: 10, qrbox: 250 },
            qrCodeMessage => {
                $("#beepSound")[0].play();
                $.get(`/barang/code/${qrCodeMessage}`, res => {
                    if (res) addBarangToTable(res);
                }).fail(() => {
                    Swal.fire('Error', 'Barang tidak ditemukan!', 'error');
                });
            },
            errorMessage => {}
        );
    }


    // ==================== FORMAT RUPIAH INPUT ====================
function formatRupiahInput(value) {
    let numberString = value.replace(/[^,\d]/g, '');
    let split = numberString.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return 'Rp ' + rupiah;
}

function parseRupiahToNumber(value) {
    return parseFloat(value.replace(/[^0-9]/g, '')) || 0;
}

// ==================== PERHITUNGAN JUMLAH BAYAR & KEMBALIAN ====================
function syncAmountPaid() {
    let grandTotalText = $('#grandTotal').text().replace(/\D/g, '');
    let grandTotal = parseFloat(grandTotalText) || 0;

    const amountPaidInput = $('#amount_paid');
    if (!amountPaidInput.is(':focus')) {
        amountPaidInput.val(formatRupiahInput(grandTotal.toString()));
    }

    updateChangeAmount();
}

function updateChangeAmount() {
    let grandTotalText = $('#grandTotal').text().replace(/\D/g, '');
    let grandTotal = parseFloat(grandTotalText) || 0;
    let amountPaid = parseRupiahToNumber($('#amount_paid').val());
    let change = amountPaid - grandTotal;

    $('#change_amount').val(formatRupiahInput((change > 0 ? change : 0).toString()));
}

// Event: tiap kali user ubah jumlah bayar, update otomatis kembalian
$('#amount_paid').on('input', function() {
    const raw = $(this).val();
    const formatted = formatRupiahInput(raw);
    $(this).val(formatted);
    updateChangeAmount();
});



});
</script>
@endpush
