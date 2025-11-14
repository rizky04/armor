@extends('layouts.main')

@section('content')
    {{-- <div class="content"> --}}
    <div class="page-header">
        <div class="page-title">
            <h4>Create Service</h4>
            <h6>Tambah data service kendaraan</h6>
        </div>
        <div class="page-btn d-flex justify-content-between align-items-center gap-2">
            {{-- <button class="btn btn-added mr-1" id="add-client-btn">+ Add Client</button> --}}
            <button class="btn btn-added" id="btn-add-kendaraan">+ Add Mobil Client</button>
        </div>

    </div>


    <div class="card">
        <div class="card-body">
            <form id="service-form">
                @csrf
                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kendaraan (Customer)</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-control"></select>
                            <div class="invalid-feedback" id="error-vehicle_id"></div>
                        </div>
                        <input type="hidden" id="id_client" name="id_client">
                    </div>

                    <!-- Kategori -->
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-control">
                                <option value="fast service">Fast Service</option>
                                <option value="inap">Inap</option>
                            </select>
                            <div class="invalid-feedback" id="error-category"></div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    {{-- <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Tanggal Service</label>
                            <input type="date" name="service_date" class="form-control">
                            <div class="invalid-feedback" id="error-service_date"></div>
                        </div>
                    </div> --}}

                    <!-- Tanggal esitmasi-->
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Estimasi Service (jika inap optional)</label>
                            <input type="date" name="estimate_date" class="form-control">
                            <div class="invalid-feedback" id="error-sestimate_date"></div>
                        </div>
                    </div>

                    <!-- Tanggal Jtauh Tempo-->
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Jatuh Tempo (jika client hutang)</label>
                            <input type="date" name="due_date" class="form-control">
                            <div class="invalid-feedback" id="error-due_date"></div>
                        </div>
                    </div>



                    <!-- Keluhan -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Keluhan</label>
                            <textarea name="complaint" class="form-control" required></textarea>
                            <div class="invalid-feedback" id="error-complaint"></div>
                        </div>
                    </div>

                    <!-- Mekanik -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Mekanik</label>
                            <select name="mechanics[]" class="form-control" multiple required>
                                @foreach ($mechanics as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-mechanics"></div>
                        </div>
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
                                            <button type="button" id="add-job" class="btn btn-sm">
                                                <i class="fa-regular fa-square-plus" style="color: green"></i>
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
                        <label>Spareparts</label>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="spareparts-table">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Sparepart</th>
                                        <th style="width:20%">Harga</th>
                                        <th style="width:15%">Qty</th>
                                        <th style="width:30%">Subtotal</th>
                                        <th class="text-center" style="width:5%; text-align:center;">
                                            <button type="button" id="add-sparepart" class="btn btn-sm">
                                                <i class="fa-regular fa-square-plus" style="color: green"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <!-- Total -->
                            <table class="table table-bordered mt-3">
                                <tbody>
                                    <tr>
                                        <th class="text-end">Total Jasa</th>
                                        <td><input type="text" id="total-jobs" class="form-control text-end fw-bold"
                                                readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end">Total Sparepart</th>
                                        <td><input type="text" id="total-spareparts"
                                                class="form-control text-end fw-bold" readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end bg-light">Grand Total</th>
                                        <td><input type="text" id="grand-total" name="grand_total"
                                                class="form-control text-end fw-bold bg-light" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="invalid-feedback" id="error-spareparts"></div>
                    </div>
                </div>
<!-- Pembayaran -->
<div class="row mt-3">
    <div class="col-lg-6 col-sm-6 col-12">
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="payment_type" id="payment_type" class="form-control" required>
                <option value="">-- Pilih Metode --</option>
                <option value="cash">Cash</option>
                <option value="transfer">Transfer</option>
                <option value="qris">QRIS</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <div class="invalid-feedback" id="error-payment_type"></div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 col-12">
        <div class="form-group">
            <label>Jumlah Bayar</label>
            <input type="number" name="amount_paid" id="amount_paid" class="form-control text-end" required>
            <div class="invalid-feedback" id="error-amount_paid"></div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 col-12">
        <div class="form-group">
            <label>Kembalian</label>
            <input type="text" name="change" id="change" class="form-control text-end" readonly>
            <div class="invalid-feedback" id="error-change"></div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 col-12">
        <div class="form-group">
            <label>Catatan (Opsional)</label>
            <textarea name="note" id="note" class="form-control" rows="2"
                placeholder="Catatan tambahan..."></textarea>
            <div class="invalid-feedback" id="error-note"></div>
        </div>
    </div>
</div>

                <!-- Submit -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-primary me-2">Save</button>
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
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
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
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
            }

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
                                    ' - ' + item.merk_barang + ' - ' + ' stok ' + item.stok_barang,
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
            <td class="text-center"><button type="button" class="btn btn-sm remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i></button></td>
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
            <td class="text-center"><button type="button" class="btn btn-sm remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i></button></td>
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

            // ============ Select2 Kendaraan ============
            $('#vehicle_id').select2({
                placeholder: 'Pilih kendaraan',
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
                            text: item.license_plate + ' - ' + item.client.nama_client,
                            id_client: item.id_client
                        }))
                    })
                }
            });

            // ================== Saat kendaraan dipilih ==================
            $('#vehicle_id').on('select2:select', function(e) {
                let data = e.params.data;
                $('#id_client').val(data.id_client); // isi otomatis id_client
            });

            $('select[name="mechanics[]"]').select2({
                placeholder: "Pilih Mekanik...",
                width: '100%'
            });

            // ============ Submit Form ============
            $('#service-form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this); // penting untuk array & file

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
                                        html: msg
                                    }).then(() => {
                                        window.location.href =
                                            "{{ route('services.index') }}";
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
                                        text: xhr.responseJSON.message || 'Terjadi kesalahan server.'
                                    });
                                }
                            }
                        });
                    }
                });
            });

                function initSelect2Client() {
    $('#client_select').select2({
        dropdownParent: $('#vehicleModal'),
        placeholder: 'Pilih Client...',
        allowClear: true,
        ajax: {
            url: "{{ route('select2.clients') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id_client,
                    text: `${item.nama_client} - ${item.no_telp ?? ''} - ${item.alamat ?? ''}`
                }))
            })
        },
        minimumInputLength: 1,
        width: '100%'
    });
}

            // tambah
            $('#btn-add-kendaraan').on('click', function() {
                initSelect2Client()
                $('#vehicleForm')[0].reset();
                $('#vehicle_id').val('');
                $('#id_client').val(null).trigger('change'); // reset select2
                $('#photo-preview').attr('src', '').hide(); // reset foto preview
                $('#vehicleModal .modal-title').text('Add Vehicle');
                $('#vehicleModal').modal('show');
            });
            // simpan
            $('#vehicleForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#vehicle_id').val();
                console.log('Vehicle ID:', id); // Debug: cek nilai id
                let url = id ? `/vehicles/${id}` : "{{ route('vehicles.store') }}";

                let formData = new FormData(this);
                if (id) formData.append('_method', 'PUT');
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#vehicleModal').modal('hide');
                        // loadVehicles(currentPage, searchQuery);
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let msg = Object.values(errors).map(e => e[0]).join('<br>');
                            Swal.fire('Error', msg, 'error');
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan saat simpan data', 'error');
                        }
                    }
                });
            });
            // preview foto di modal ketika klik thumbnail
            $(document).on('click', '.preview-photo', function() {
                let src = $(this).data('src');
                $('#modal-photo').attr('src', src);
                $('#photoModal').modal('show');
            });

        });
    </script>
@endpush
