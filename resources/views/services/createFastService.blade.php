@extends('layouts.main')

@section('content')
    {{-- <div class="content"> --}}
    <div class="page-header">
        <div class="page-title">
            <h4>Create Service</h4>
            <h6>Tambah data service cepat kendaraan</h6>
        </div>
        <div class="page-btn d-flex justify-content-between align-items-center gap-2">
            {{-- <button class="btn btn-added mr-1" id="add-client-btn">+ Add Client</button> --}}
            <button class="btn btn-added" id="btn-add-kendaraan">+ Tambah Data Mobil Client</button>
        </div>

    </div>


    <div class="card">
        <div class="card-body">
            <form id="service-form">
                @csrf
                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Kendaraan (Customer)</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-control" required></select>
                            <div class="invalid-feedback" id="error-vehicle_id"></div>
                        </div>
                        <input type="hidden" id="id_client" name="id_client">
                    </div>

                    <!-- Mekanik -->
                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Mekanik</label>
                            <select name="mechanics[]" id="mekanik" class="form-control" multiple required>
                                @foreach ($mechanics as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-mechanics"></div>
                        </div>
                    </div>
                </div>

                  <!-- Spareparts -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <label>Spareparts</label>
                        <div class="table-responsive mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Scan Barcode Barang</label>
                                    <input type="text" id="scanBarcode" class="form-control"
                                        placeholder="Arahkan scanner ke sini..." autofocus>
                                    <audio id="beepSound" src="{{ asset('sounds/beep.mp3') }}" preload="auto"></audio>
                                </div>
                            </div>
                            <table class="table table-bordered" id="spareparts-table">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Sparepart</th>
                                        <th style="width:20%">Harga</th>
                                        <th style="width:15%">Qty</th>
                                        <th style="width:30%">Subtotal</th>
                                        <th class="text-center" style="width:5%; text-align:center;">
                                            <button type="button" id="add-sparepart" class="btn btn-sm btn-primary">
                                                <i class="fa-regular fa-square-plus"></i>
                                                Tambah Spearpart
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="invalid-feedback" id="error-spareparts"></div>
                    </div>
                </div>

                <!-- Jobs -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <label>Service Jasa</label>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="jobs-table">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Jasa</th>
                                        <th style="width:20%">Harga</th>
                                        <th style="width:15%">Qty</th>
                                        <th style="width:30%">Subtotal</th>
                                        <th class="text-center" style="width:5%; text-align:center;">
                                            <button type="button" id="add-job" class="btn btn-sm btn-primary">
                                                <i class="fa-regular fa-square-plus"></i>
                                                Tambah Jasa
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="invalid-feedback" id="error-jobs"></div>
                    </div>
                </div>

                 <!-- Spareparts -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <div class="table-responsive mb-3">
                            <!-- Total -->
                            <table class="table table-bordered mt-3">
                                <tbody>
                                    <tr>
                                        <th class="text-end">Total Sparepart</th>
                                        <td><input type="text" id="total-spareparts"
                                                class="form-control text-end fw-bold" readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end">Total Jasa</th>
                                        <td><input type="text" id="total-jobs" class="form-control text-end fw-bold"
                                                readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end bg-light">Grand Total</th>
                                        <td><input type="text" id="grand-total" name="grand_total"
                                                class="form-control text-end fw-bold bg-light" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- Pembayaran -->
                <div class="row mt-3">
                    <div class="col-lg-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label>Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="belum bayar">Belum Bayar</option>
                                <option value="lunas">Lunas</option>
                                <option value="hutang">Hutang</option>
                            </select>
                            <div class="invalid-feedback" id="error-status_pembayaran"></div>
                        </div>
                    </div>

                    <!-- Tanggal Jtauh Tempo-->
                    <div class="col-lg-4 col-sm-4 col-12" id="due_date_div">
                        <div class="form-group">
                            <label>Jatuh Tempo (jika client hutang)</label>
                            <input type="date" name="due_date" class="form-control">
                            <div class="invalid-feedback" id="error-due_date"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="payment_type" id="payment_type" class="form-control" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                            <div class="invalid-feedback" id="error-payment_type"></div>
                        </div>
                    </div>


                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Jumlah Bayar</label>
                            <input type="text" id="amount_paid_display" class="form-control text-end" required>
                            <input type="hidden" id="amount_paid" name="amount_paid">
                            <div class="invalid-feedback" id="error-amount_paid"></div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="text" id="change_display" class="form-control text-end bg-light" readonly>
                            <input type="hidden" id="change" name="change">
                            <div class="invalid-feedback" id="error-change"></div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <button type="submit" id="btn-save" class="btn btn-primary me-2">Save</button>
                        <a href="{{ route('services.create') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
    {{-- </div> --}}
    @include('services.modal.create-kendaraan')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(document).ready(function() {

            initVehicleSelect();

         // ==================== TAMPILKAN JATUH TEMPO SAAT HUTANG ====================
$('#due_date_div').hide(); // sembunyikan dulu di awal

$('#status_pembayaran').on('change', function() {
    const status = $(this).val();

    if (status === 'hutang') {
        $('#due_date_div').slideDown(200);
        $('[name="due_date"]').attr('required', true);

        // otomatis isi tanggal +7 hari
        const today = new Date();
        today.setDate(today.getDate() + 7);
        const next7 = today.toISOString().split('T')[0];
        $('[name="due_date"]').val(next7);

    } else {
        $('#due_date_div').slideUp(200);
        $('[name="due_date"]').val('').removeAttr('required');
    }
});

// Jalankan di awal agar sinkron kalau status default bukan kosong
$('#status_pembayaran').trigger('change');


            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // === Format Rupiah (sudah ada, gunakan yang sama)
            function parseRupiah(str) {
                if (!str) return 0;
                return parseInt(str.replace(/[^0-9]/g, '')) || 0;
            }

            // === Update otomatis Amount Paid & Kembalian ===
            function updatePaymentFields(forceUpdate = false) {
                const grandTotal = parseRupiah($('#grand-total').val());
                let amountPaid = parseRupiah($('#amount_paid_display').val());

                // Jika amount_paid kosong ATAU mode auto-update aktif (forceUpdate = true)
                if (!amountPaid || forceUpdate) {
                    amountPaid = grandTotal;
                    $('#amount_paid_display').val(formatRupiah(amountPaid));
                }

                const change = amountPaid - grandTotal;
                $('#change_display').val(formatRupiah(change > 0 ? change : 0));
                $('#amount_paid').val(amountPaid);
                $('#change').val(change > 0 ? change : 0);
            }




            // ============ Hitung Total ============
            function calculateGrandTotal() {
                let totalSpareparts = 0,
                    totalJobs = 0;

                $('#spareparts-table tbody tr').each(function(i, el) {
                    let qty = parseFloat($(el).find('.qty').val()) || 0;
                    let price = parseFloat($(el).find('.price').data('raw')) || 0;
                    let rowTotal = qty * price;
                    $(el).find('.subtotal').val(formatRupiah(rowTotal));
                    $(el).find('.subtotal-hidden').val(rowTotal);
                    totalSpareparts += rowTotal;
                });

                $('#jobs-table tbody tr').each(function(i, el) {
                    let qty = parseFloat($(el).find('.job-qty').val()) || 0;
                    let price = parseFloat($(el).find('.job-price').data('raw')) || 0;
                    let rowTotal = qty * price;
                    $(el).find('.job-subtotal').val(formatRupiah(rowTotal));
                    $(el).find('.job-subtotal-hidden').val(rowTotal);
                    totalJobs += rowTotal;
                });

                $('#total-spareparts').val(formatRupiah(totalSpareparts));
                $('#total-jobs').val(formatRupiah(totalJobs));
                $('#grand-total').val(formatRupiah(totalSpareparts + totalJobs));

                updatePaymentFields(true); // ⬅️ panggil di sini
            }


            // === Saat jumlah bayar diubah manual ===
            $(document).on('input', '#amount_paid_display', function() {
                updatePaymentFields();
            });

            // mekanik
            $('select[name="mechanics[]"]').select2({
                placeholder: "Pilih Mekanik...",
                width: '100%'
            });

            function initJobSelect(el) {
                el.select2({
                        placeholder: 'Pilih atau ketik jasa...',
                        tags: true,
                        ajax: {
                            url: "{{ route('select2.jasa') }}",
                            dataType: 'json',
                            delay: 250,
                            data: params => ({
                                q: params.term
                            }),
                            processResults: data => ({
                                results: data.map(item => ({
                                    id: item.id_jasa,
                                    text: item.nama_jasa + ' - ' + item.harga_jasa,
                                    price: item.harga_jasa
                                }))
                            })
                        },
                        createTag: params => ({
                            id: 'new:' + params.term,
                            text: params.term + ' (buat baru)',
                            newOption: true
                        })
                    })
                    .on('select2:select', function(e) {
                        handleSelectJasa($(this), e.params.data);
                    })
                    .on('select2:open', function() {
                        let select = $(this);
                        let input = $('.select2-search__field');
                        input.off('keyup.select2enter').on('keyup.select2enter', function(e) {
                            if (e.key === 'Enter') {
                                let term = $(this).val().trim();
                                if (term !== '') {
                                    let newData = {
                                        id: 'new:' + term,
                                        text: term + ' (buat baru)',
                                        newOption: true
                                    };
                                    select.append(new Option(newData.text, newData.id, true, true))
                                        .trigger({
                                            type: 'select2:select',
                                            params: {
                                                data: newData
                                            }
                                        });
                                    select.select2('close');
                                }
                            }
                        });
                    })
                    // ✅ Tambah fallback: kalau diklik tapi event gak nyantol
                    .on('select2:close', function() {
                        let val = $(this).val();
                        if (val && typeof val === 'string' && val.startsWith('new:')) {
                            let data = {
                                id: val,
                                text: val.replace('new:', '') + ' (buat baru)',
                                newOption: true
                            };
                            handleSelectJasa($(this), data);
                        }
                    });

                // Fungsi logika utama (supaya gak double code)
                function handleSelectJasa(select, data) {
                    let row = select.closest('tr');

                    if (typeof data.id === 'string' && data.id.startsWith('new:')) {
                        Swal.fire({
                            title: 'Jasa baru',
                            html: `
                    <input type="text" id="nama_jasa" class="swal2-input"
                           value="${data.text.replace(' (buat baru)', '')}">
                    <input type="number" id="harga_jasa" class="swal2-input" placeholder="Harga jasa">
                `,
                            focusConfirm: false,
                            preConfirm: () => {
                                return {
                                    nama_jasa: document.getElementById('nama_jasa').value,
                                    harga_jasa: document.getElementById('harga_jasa').value
                                };
                            },
                            confirmButtonText: 'Simpan'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "{{ route('jasa.store.ajax') }}",
                                    type: "POST",
                                    data: result.value,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(res) {
                                        if (res.status) {
                                            const newOption = new Option(res.data.nama_jasa, res
                                                .data.id_jasa, true, true);
                                            select.append(newOption).trigger('change');

                                            row.find('.job-price')
                                                .val(formatRupiah(res.data.harga_jasa))
                                                .data('raw', res.data.harga_jasa);
                                            row.find('.job-price-hidden').val(res.data
                                                .harga_jasa);
                                            row.find('.job-qty').val(1);
                                            calculateGrandTotal();

                                            Swal.fire('Berhasil', 'Jasa baru ditambahkan',
                                                'success');
                                        }
                                    },
                                    error: () => Swal.fire('Gagal',
                                        'Tidak bisa menyimpan jasa baru', 'error')
                                });
                            }
                        });
                    } else {
                        row.find('.job-price')
                            .val(formatRupiah(data.price))
                            .data('raw', data.price);
                        row.find('.job-price-hidden').val(data.price);
                        row.find('.job-qty').val(1);
                        calculateGrandTotal();
                    }
                }
            }



            function initSparepartSelect(el) {
                el.select2({
                    placeholder: 'Pilih sparepart',
                    ajax: {
                        url: "{{ route('select2.barang') }}",
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term
                        }),
                        processResults: data => ({
                            results: data.map(item => ({
                                id: item.id_barang,
                                text: item.id_barang + ' - ' + item.nama_barang +
                                    ' - ' + item.kode_barang + ' - ' + item.jenis +
                                    ' - ' + item.merk_barang + ' - ' + ' stok ' + item
                                    .stok_barang + ' - ' + item.keterangan + ' - ' + + 'Harga: Rp ' + item
                                    .harga_jual.toString().replace(
                                        /\B(?=(\d{3})+(?!\d))/g, "."),
                                price: item.harga_jual,
                                purchase_price: item.harga_kulak
                            }))
                        })
                    }
                }).on('select2:select', function(e) {
                    let data = e.params.data;
                    let row = $(this).closest('tr');
                    row.find('.price').val(formatRupiah(data.price)).data('raw', data.price);
                    row.find('.price-hidden').val(data.price);
                    row.find('.purchase-price-hidden').val(data.purchase_price);
                    row.find('.qty').val(1);
                    calculateGrandTotal();
                });
            }

            // ============ Tambah Row ============
            $('#add-job').click(function() {
                let row = `
        <tr>
            <td><select name="jobs[${Date.now()}][id]" class="form-control job-select"></select></td>
            <td>
                <input type="text" class="form-control job-price text-end" readonly>
                <input type="hidden" name="jobs[${Date.now()}][price]" class="job-price-hidden">
            </td>
            <td><input type="number" name="jobs[${Date.now()}][qty]" class="form-control job-qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control job-subtotal text-end" readonly>
                <input type="hidden" name="jobs[${Date.now()}][subtotal]" class="job-subtotal-hidden">
            </td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i>Hapus</button></td>
        </tr>`;
                $('#jobs-table tbody').append(row);
                initJobSelect($('#jobs-table .job-select').last());
            });

            $('#add-sparepart').click(function() {
                let row = `
        <tr>
            <td><select name="spareparts[${Date.now()}][id]" class="form-control sparepart-select"></select></td>
            <td>
                <input type="text" class="form-control price text-end" readonly>
                <input type="hidden" name="spareparts[${Date.now()}][price]" class="price-hidden">
                <input type="hidden" name="spareparts[${Date.now()}][purchase_price]" class="purchase-price-hidden">
            </td>
            <td><input type="number" name="spareparts[${Date.now()}][qty]" class="form-control qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control subtotal text-end" readonly>
                <input type="hidden" name="spareparts[${Date.now()}][subtotal]" class="subtotal-hidden">
            </td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i>Hapus</button></td>
        </tr>`;
                $('#spareparts-table tbody').append(row);
                initSparepartSelect($('#spareparts-table .sparepart-select').last());
            });

            // ============ Event ============
            $(document).on('input', '.qty, .job-qty', calculateGrandTotal);
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });


            function initVehicleSelect() {
                $('#vehicle_id').select2({
                        placeholder: 'Pilih kendaraan...',
                        tags: true, // aktifkan opsi buat baru
                        ajax: {
                            url: "{{ route('select2.vehicles') }}",
                            dataType: 'json',
                            delay: 250,
                            data: params => ({
                                q: params.term
                            }),
                            processResults: data => ({
                                results: data.map(item => ({
                                    id: item.id,
                                    text: `${item.license_plate} - ${item.client.nama_client}`,
                                    id_client: item.id_client
                                }))
                            })
                        },
                        createTag: params => ({
                            id: 'new:' + params.term,
                            text: params.term + ' (tambah kendaraan baru)',
                            newOption: true
                        })
                    })
                    .on('select2:select', function(e) {
                        const data = e.params.data;

                        // Kalau pilih data existing → isi id_client
                        if (!data.id.startsWith('new:')) {
                            $('#id_client').val(data.id_client);
                            return;
                        }

                        // Kalau data baru → buka modal tambah kendaraan
                        const newPlate = data.text.replace(' (tambah kendaraan baru)', '');

                        Swal.fire({
                            title: 'Tambah Kendaraan Baru',
                            html: `
                <div class="text-start">
                    <label>Client</label>
                    <select id="swal_client" class="form-control"></select>
                    <br>
                    <label>Plat Nomor</label>
                    <input type="text" id="swal_plate" class="form-control" value="${newPlate}">
                </div>
            `,
                            didOpen: () => {
                                // Inisialisasi select2 client di dalam Swal
                                $('#swal_client').select2({
                                    dropdownParent: $('.swal2-container'),
                                    ajax: {
                                        url: "{{ route('select2.clients') }}",
                                        dataType: 'json',
                                        delay: 250,
                                        data: params => ({
                                            q: params.term
                                        }),
                                        processResults: data => ({
                                            results: data.map(item => ({
                                                id: item.id_client,
                                                text: `${item.nama_client} - ${item.no_telp ?? ''}`
                                            }))
                                        })
                                    },
                                    placeholder: 'Pilih client...',
                                    width: '100%'
                                });
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                            preConfirm: () => {
                                return {
                                    id_client: $('#swal_client').val(),
                                    license_plate: $('#swal_plate').val().trim()
                                };
                            }
                        }).then(result => {
                            if (result.isConfirmed) {
                                const formData = result.value;

                                if (!formData.id_client || !formData.license_plate) {
                                    Swal.fire('Gagal', 'Client dan plat nomor wajib diisi.', 'warning');
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('vehicles.store') }}",
                                    method: 'POST',
                                    data: formData,
                                    success: function(res) {
                                        if (res.status) {
                                            const newOpt = new Option(
                                                `${res.data.license_plate} - ${res.data.client.nama_client}`,
                                                res.data.id,
                                                true,
                                                true
                                            );
                                            $('#vehicle_id').append(newOpt).trigger(
                                                'change');
                                            $('#id_client').val(res.data.id_client);

                                            Swal.fire('Berhasil',
                                                'Kendaraan baru berhasil ditambahkan',
                                                'success');
                                        } else {
                                            Swal.fire('Error', res.message, 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Terjadi kesalahan saat menyimpan kendaraan',
                                            'error');
                                    }
                                });
                            }
                        });
                    });
            }


            // ============ Submit Form ============
            $('#service-form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                Swal.fire({
                    title: 'Yakin simpan data?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('services.store') }}",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status) {
                                    let msg = res.message;
                                    if (res.warning) {
                                        msg += "<br><b style='color:red'>" + res
                                            .warning + "</b>";
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        html: msg,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // 🔒 Disable tombol Save
                                        $('#btn-save').prop('disabled', true)
                                            .text('Tersimpan');

                                        // 🖨 Tambahkan tombol Print di sebelahnya
                                        if ($('#btn-print').length === 0) {
                                            $('#btn-save').after(`
                                    <button type="button" id="btn-print" class="btn btn-info ms-2">
                                        <i class="fa fa-print"></i> Print Nota
                                    </button>
                                `);
                                        }

                                        // ✅ Event tombol Print
                                        $('#btn-print').on('click', function() {
                                            window.open(
                                                `/services/${res.data.id}/print`,
                                                '_blank');

                                            // 🔁 Reset form setelah print
                                            setTimeout(() => {
                                                $('#service-form')[
                                                        0]
                                                    .reset();
                                                $('.select2')
                                                    .val(null)
                                                    .trigger(
                                                        'change'
                                                        );
                                                $('#mekanik')
                                                    .val(null)
                                                    .trigger(
                                                        'change'
                                                        );
                                                $('#vehicle_id')
                                                    .val(null)
                                                    .trigger(
                                                        'change'
                                                        );
                                                $('#id_client')
                                                    .val(null)
                                                    .trigger(
                                                        'change'
                                                        );
                                                $('#id_client')
                                                    .val('');
                                                $('#jobs-table tbody')
                                                    .empty();
                                                $('#spareparts-table tbody')
                                                    .empty();
                                                $('#total-jobs, #total-spareparts, #grand-total')
                                                    .val('');
                                                $('#amount_paid_display, #change_display')
                                                    .val('');


                                                // aktifkan kembali tombol Save
                                                $('#btn-save')
                                                    .prop(
                                                        'disabled',
                                                        false)
                                                    .text(
                                                        'Simpan'
                                                        );
                                                $('#btn-print')
                                                    .remove();

                                                // fokus ke input pertama
                                                $('#service-form input:first')
                                                    .focus();
                                            }, 1000);
                                        });
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: res.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                $('.invalid-feedback').text('');
                                $('.form-control').removeClass('is-invalid');

                                if (xhr.status === 422) {
                                    $.each(xhr.responseJSON.errors, function(key,
                                    value) {
                                        $(`[name="${key}"]`).addClass(
                                            'is-invalid');
                                        $(`#error-${key}`).text(value[0]);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: xhr.responseJSON.message ||
                                            'Terjadi kesalahan server.'
                                    });
                                }
                            }
                        });
                    }
                });
            });


            // ============ Submit Form ============

            // ==================== SCAN BARCODE ====================
            $('#scanBarcode').on('keypress', function(e) {
                if (e.which === 13) { // Enter otomatis dari scanner
                    e.preventDefault();
                    const kode = $(this).val().trim();
                    if (kode !== '') {
                        fetchBarangByQR(kode);
                        $(this).val('');
                    }
                }
            });

            // ==================== PILIH MANUAL SPAREPART ====================
            $('#manualSparepart').select2({
                placeholder: 'Cari sparepart...',
                ajax: {
                    url: "{{ route('select2.barang') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id_barang,
                            text: `${item.id_barang} - ${item.nama_barang} - stok ${item.stok_barang}`,
                            harga: item.harga_jual,
                            harga_kulak: item.harga_kulak,
                            stok: item.stok_barang,
                            jenis: item.jenis
                        }))
                    })
                },
                width: '100%'
            }).on('select2:select', function(e) {
                const item = e.params.data;
                addBarangToTable({
                    id: item.id,
                    nama: item.text,
                    harga: item.harga,
                    harga_kulak: item.harga_kulak,
                    stok: item.stok,
                    jenis: item.jenis
                });
                $(this).val(null).trigger('change'); // reset select
            });

            // ==================== FETCH BARANG DARI BARCODE ====================
            async function fetchBarangByQR(kode) {
                try {
                    const res = await fetch(`/api/barang/by-qr/${kode}`);
                    if (!res.ok) throw new Error('Barang tidak ditemukan');

                    const item = await res.json();
                    addBarangToTable({
                        id: item.id_barang,
                        nama: `${item.id_barang} - ${item.kode_barang} - ${item.nama_barang} - ${item.jenis} - ${item.keterangan} - (Stok: ${item.stok_barang ?? 0}) - Harga: Rp ${item.harga_jual.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")} `,
                        harga: item.harga_jual,
                        harga_kulak: item.harga_kulak,
                        stok: item.stok_barang ?? 0,
                        jenis: item.jenis,
                    });

                    // Bunyi beep
                    const beepSound = document.getElementById("beepSound");
                    if (beepSound) beepSound.play();

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

            // ==================== TAMBAH BARANG KE TABEL ====================
            function addBarangToTable(item) {
                const rowId = Date.now();

                // Cek apakah barang sudah ada → tambahkan qty saja
                let existingRow = null;
                $('#spareparts-table tbody tr').each(function() {
                    const val = $(this).find('.sparepart-select').val();
                    if (val == item.id) {
                        existingRow = $(this);
                    }
                });

                if (existingRow) {
                    let qtyInput = existingRow.find('.qty');
                    qtyInput.val(parseInt(qtyInput.val()) + 1);

                    // Efek highlight hijau sebentar
                    existingRow.addClass('table-success');
                    setTimeout(() => existingRow.removeClass('table-success'), 700);

                    calculateGrandTotal();
                    return;
                }

                // Kalau belum ada, tambahkan row baru
                const row = `
        <tr class="table-success">
            <td>
                <select name="spareparts[${rowId}][id]" class="form-control sparepart-select">
                    <option value="${item.id}" selected>${item.nama}</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control price text-end" readonly value="${formatRupiah(item.harga)}" data-raw="${item.harga}">
                <input type="hidden" name="spareparts[${rowId}][price]" class="price-hidden" value="${item.harga}">
                <input type="hidden" name="spareparts[${rowId}][purchase_price]" class="purchase-price-hidden" value="${item.harga_kulak}">
            </td>
            <td><input type="number" name="spareparts[${rowId}][qty]" class="form-control qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control subtotal text-end" readonly value="${formatRupiah(item.harga)}">
                <input type="hidden" name="spareparts[${rowId}][subtotal]" class="subtotal-hidden" value="${item.harga}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm remove-row">
                    <i class="fa-regular fa-square-minus" style="color: red"></i>
                </button>
            </td>
        </tr>`;

                $('#spareparts-table tbody').append(row);

                // Efek highlight baris baru
                setTimeout(() => {
                    $('#spareparts-table tbody tr.table-success').removeClass('table-success');
                }, 800);

                // Scroll otomatis ke bawah
                $('#spareparts-table').closest('.table-responsive').animate({
                    scrollTop: $('#spareparts-table tbody tr:last').offset().top
                }, 300);

                calculateGrandTotal();
            }

        });
    </script>
@endpush
