@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Service</h4>
            <h6>Perbarui data service</h6>
        </div>
    </div>

    <form id="form-service" method="POST">
        @csrf
        @method('PUT')

        {{-- ===================== --}}
        {{-- Info Service --}}
        {{-- ===================== --}}
        <div class="card p-3 mb-3">
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label>Vehicle</label>
                    <select name="vehicle_id" id="vehicle_id" class="form-control select2-vehicle">
                        <option value="{{ $service->vehicle->id }}">
                            {{ $service->vehicle->license_plate }} - {{ $service->vehicle->client->nama_client }}
                        </option>
                    </select>
                    <input type="hidden" id="id_client" name="id_client"
                        value="{{ $service->vehicle->client->id_client }}">
                </div>

                <div class="mb-3 col-md-6">
                    <label>Kategori</label>
                    <input type="text" name="category" value="{{ $service->category }}" class="form-control" required>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Tanggal Servie</label>
                    <input type="date" name="service_date"
                        value="{{ \Carbon\Carbon::parse($service->service_date)->format('Y-m-d') }}" class="form-control"
                        required>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Estimasi Service(jika inap optional)</label>
                    <input type="date" name="estimate_date"
                        value="{{ \Carbon\Carbon::parse($service->estimate_date)->format('Y-m-d') }}" class="form-control"
                        required>
                </div>
                <div class="mb-3 col-md-4">
                    <label>Jatuh Tempo (jika client hutang)</label>
                    <input type="date" name="due_date"
                        value="{{ \Carbon\Carbon::parse($service->due_date)->format('Y-m-d') }}" class="form-control">
                    <div class="invalid-feedback" id="error-due_date"></div>
                </div>
                <div class="mb-3 col-md-12">
                    <label>Keluhan</label>
                    <textarea name="complaint" class="form-control">{{ $service->complaint }}</textarea>
                </div>
                <div class="col-md-12">
                    <label>Mechanics</label>
                    <select name="mechanics[]" class="form-control select2-mechanic" multiple>
                        @foreach ($service->mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}" selected>{{ $mechanic->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- Client / Vehicle / Mechanic --}}
        {{-- ===================== --}}
        {{-- <div class="card p-3 mb-3">
            <div class="row">


            </div>
        </div> --}}

        {{-- ===================== --}}
        {{-- Table Jasa --}}
        {{-- ===================== --}}
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>Pekerjaan / Jasa</h5>
                <button type="button" id="add-jasa" class="btn btn-sm"><i class="fa-regular fa-square-plus"
                        style="color: green"></i></button>
            </div>
            <table class="table table-bordered" id="table-jasa">
                <thead>
                    <tr>
                        <th>Pekerjaan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($service->jobs as $job)
                        <tr data-key="{{ $loop->index }}">
                            <td>
                                <select name="jobs[{{ $loop->index }}][id_jasa]" class="form-control select2-jasa">
                                    <option value="{{ $job->jasa->id_jasa }}" selected>{{ $job->jasa->nama_jasa }}
                                    </option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control job-price text-end" readonly
                                    value="Rp{{ number_format($job->price, 0, ',', '.') }}">
                                <input type="hidden" name="jobs[{{ $loop->index }}][price]" class="job-price-hidden"
                                    value="{{ $job->price }}">
                            </td>
                            <td><input type="number" name="jobs[{{ $loop->index }}][qty]"
                                    class="form-control job-qty text-end" value="{{ $job->qty }}" min="1"></td>
                            <td>
                                <input type="text" class="form-control job-subtotal text-end" readonly
                                    value="Rp{{ number_format($job->subtotal, 0, ',', '.') }}">
                                <input type="hidden" name="jobs[{{ $loop->index }}][subtotal]"
                                    class="job-subtotal-hidden" value="{{ $job->subtotal }}">
                            </td>
                            <td><button type="button" class="btn btn-sm remove-row"><i class="fa-regular fa-square-minus"
                                        style="color: red"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===================== --}}
        {{-- Table Sparepart --}}
        {{-- ===================== --}}
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>Spareparts</h5>
                <button type="button" id="add-sparepart" class="btn btn-sm"><i class="fa-regular fa-square-plus"
                        style="color: green"></i></button>
            </div>
            <table class="table table-bordered" id="table-sparepart">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($service->spareparts as $spare)
                        <tr data-key="{{ $loop->index }}">
                            <td>
                                <select name="spareparts[{{ $loop->index }}][id_barang]"
                                    class="form-control select2-sparepart">
                                    <option value="{{ $spare->barang->id_barang }}" selected>
                                        {{ $spare->barang->nama_barang }}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control price text-end" readonly
                                    value="Rp{{ number_format($spare->price, 0, ',', '.') }}">
                                <input type="hidden" name="spareparts[{{ $loop->index }}][price]" class="price-hidden"
                                    value="{{ $spare->price }}">
                                <input type="hidden" name="spareparts[{{ $loop->index }}][purchase_price]"
                                    class="purchase-price-hidden" value="{{ $spare->purchase_price }}">
                            </td>
                            <td><input type="number" name="spareparts[{{ $loop->index }}][qty]"
                                    class="form-control qty text-end" value="{{ $spare->qty }}" min="1"></td>
                            <td>
                                <input type="text" class="form-control subtotal text-end" readonly
                                    value="Rp{{ number_format($spare->subtotal, 0, ',', '.') }}">
                                <input type="hidden" name="spareparts[{{ $loop->index }}][subtotal]"
                                    class="subtotal-hidden" value="{{ $spare->subtotal }}">
                            </td>
                            <td><button type="button" class="btn btn-sm remove-row"><i
                                        class="fa-regular fa-square-minus" style="color: red"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===================== --}}
        {{-- Grand Total --}}
        {{-- ===================== --}}
        <div class="card p-3">
            <div class="row">
                <div class="col-md-4">
                    <label>Total Jasa</label>
                    <input type="text" id="total-jobs" class="form-control text-end" readonly>
                </div>
                <div class="col-md-4">
                    <label>Total Spareparts</label>
                    <input type="text" id="total-spareparts" class="form-control text-end" readonly>
                </div>
                <div class="col-md-4">
                    <label>Grand Total</label>
                    <input type="text" id="grand-total" class="form-control text-end" readonly>
                </div>
            </div>
        </div>

      <!-- Pembayaran -->
<div class="row mt-3">
    {{-- Status Pembayaran --}}
    <div class="col-lg-4 col-sm-4 col-12">
        <div class="form-group">
            <label>Status Pembayaran</label>
            <select name="status_pembayaran" id="status_pembayaran" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="belum bayar" {{ $service->status_bayar == 'belum bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="lunas" {{ $service->status_bayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="hutang" {{ $service->status_bayar == 'hutang' ? 'selected' : '' }}>Hutang</option>
            </select>
            <div class="invalid-feedback" id="error-status_pembayaran"></div>
        </div>
    </div>

    {{-- Jatuh Tempo (Hanya jika hutang) --}}
    <div class="col-lg-4 col-sm-4 col-12" id="due_date_div"
        style="display: {{ $service->status_bayar == 'hutang' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Jatuh Tempo (jika client hutang)</label>
            <input type="date" name="due_date" class="form-control" value="{{ $service->due_date }}">
            <div class="invalid-feedback" id="error-due_date"></div>
        </div>
    </div>

    {{-- Metode Pembayaran (Hanya jika lunas) --}}
    <div class="col-lg-4 col-sm-4 col-12 payment-field"
        style="display: {{ $service->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="payment_type" id="payment_type" class="form-control">
                <option value="">-- Pilih Metode --</option>
                <option value="cash" {{ $service->payment?->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="transfer" {{ $service->payment?->payment_type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="qris" {{ $service->payment?->payment_type == 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
            <div class="invalid-feedback" id="error-payment_type"></div>
        </div>
    </div>

    {{-- Jumlah Bayar --}}
    <div class="col-lg-6 col-sm-6 col-12 payment-field"
        style="display: {{ $service->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Jumlah Bayar</label>
            <input type="text" id="amount_paid_display" class="form-control text-end"
                value="{{ $service->payment?->amount_paid ? number_format($service->payment->amount_paid, 0, ',', '.') : '' }}">
            <input type="hidden" id="amount_paid" name="amount_paid"
                value="{{ $service->payment?->amount_paid }}">
            <div class="invalid-feedback" id="error-amount_paid"></div>
        </div>
    </div>

    {{-- Kembalian --}}
    <div class="col-lg-6 col-sm-6 col-12 payment-field"
        style="display: {{ $service->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Kembalian</label>
            <input type="text" id="change_display" class="form-control text-end bg-light"
                value="{{ $service->payment?->change_amount ? number_format($service->payment->change_amount, 0, ',', '.') : '0' }}"
                readonly>
            <input type="hidden" id="change" name="change" value="{{ $service->payment?->change_amount ?? 0 }}">
            <div class="invalid-feedback" id="error-change"></div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="col-lg-12 col-sm-12 col-12 payment-field"
        style="display: {{ $service->status_bayar == 'lunas' ? 'block' : 'none' }}">
        <div class="form-group">
            <label>Catatan (Opsional)</label>
            <textarea name="note" id="note" class="form-control" rows="2"
                placeholder="Catatan tambahan...">{{ $service->payment?->note }}</textarea>
            <div class="invalid-feedback" id="error-note"></div>
        </div>
    </div>
</div>


        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-success">Update Service</button>
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
    <!-- Script -->

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

            //             $('#due_date_div').hide(); // sembunyikan dulu di awal

            // $('#status_pembayaran').on('change', function() {
            //     const status = $(this).val();

            //     if (status === 'hutang') {
            //         $('#due_date_div').slideDown(200);
            //         $('[name="due_date"]').attr('required', true);

            //         // otomatis isi tanggal +7 hari
            //         const today = new Date();
            //         today.setDate(today.getDate() + 7);
            //         const next7 = today.toISOString().split('T')[0];
            //         $('[name="due_date"]').val(next7);

            //     } else {
            //         $('#due_date_div').slideUp(200);
            //         $('[name="due_date"]').val('').removeAttr('required');
            //     }
            // });

            // Jalankan di awal agar sinkron kalau status default bukan kosong
            $('#status_pembayaran').trigger('change');

            initVehicleSelect();

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


            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function calculateGrandTotal() {
                let totalJobs = 0,
                    totalSpareparts = 0;

                $('#table-jasa tbody tr').each(function() {
                    let qty = parseFloat($(this).find('.job-qty').val()) || 0;
                    let price = parseFloat($(this).find('.job-price-hidden').val()) || 0;
                    let subtotal = qty * price;
                    $(this).find('.job-subtotal').val(formatRupiah(subtotal));
                    $(this).find('.job-subtotal-hidden').val(subtotal);
                    totalJobs += subtotal;
                });

                $('#table-sparepart tbody tr').each(function() {
                    let qty = parseFloat($(this).find('.qty').val()) || 0;
                    let price = parseFloat($(this).find('.price-hidden').val()) || 0;
                    let subtotal = qty * price;
                    $(this).find('.subtotal').val(formatRupiah(subtotal));
                    $(this).find('.subtotal-hidden').val(subtotal);
                    totalSpareparts += subtotal;
                });

                $('#total-jobs').val(formatRupiah(totalJobs));
                $('#total-spareparts').val(formatRupiah(totalSpareparts));
                $('#grand-total').val(formatRupiah(totalJobs + totalSpareparts));
            }

            function initSelect2Client() {
                $('.select2-client').select2({
                    placeholder: 'Pilih Client',
                    ajax: {
                        url: '{{ route('select2.clients') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: data
                        })
                    }
                });
            }

            function initSelect2Vehicle() {
                $('.select2-vehicle').select2({
                    placeholder: 'Pilih Vehicle',
                    ajax: {
                        url: "{{ route('select2.vehicles') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: data.map(item => ({
                                id: item.id,
                                text: item.license_plate + ' - ' + item.client
                                    .nama_client,
                                id_client: item.id_client
                            }))
                        })
                    }
                });
            }
            // ================== Saat kendaraan dipilih ==================
            $('#vehicle_id').on('select2:select', function(e) {
                let data = e.params.data;
                $('#id_client').val(data.id_client); // isi otomatis id_client
            });

            function initSelect2Mechanic() {
                $('.select2-mechanic').select2({
                    placeholder: 'Pilih Mechanic',
                    ajax: {
                        url: '{{ route('select2.mechanics') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: data => ({
                            results: data
                        })
                    }
                });
            }

            function initSelect2Jasa(el) {
                el.select2({
                    placeholder: 'Pilih pekerjaan',
                    ajax: {
                        url: '{{ route('select2.jasa') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term
                        }),
                        processResults: data => ({
                            results: data.map(item => ({
                                id: item.id_jasa,
                                text: item.nama_jasa,
                                price: item.harga_jasa
                            }))
                        })
                    }
                }).on('select2:select', function(e) {
                    let data = e.params.data;
                    let row = $(this).closest('tr');
                    row.find('.job-price').val(formatRupiah(data.price));
                    row.find('.job-price-hidden').val(data.price);
                    row.find('.job-qty').val(1);
                    calculateGrandTotal();
                });
            }

            function initSelect2Sparepart(el) {
                el.select2({
                    placeholder: 'Pilih sparepart',
                    ajax: {
                        url: '{{ route('select2.barang') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term
                        }),
                        processResults: data => ({
                            results: data.map(item => ({
                                id: item.id_barang,
                                text: item.kode_barang + ' - ' + item.nama_barang,
                                price: item.harga_jual,
                                purchase_price: item.harga_kulak
                            }))
                        })
                    }
                }).on('select2:select', function(e) {
                    let data = e.params.data;
                    let row = $(this).closest('tr');
                    row.find('.price').val(formatRupiah(data.price));
                    row.find('.price-hidden').val(data.price);
                    row.find('.purchase-price-hidden').val(data.purchase_price);
                    row.find('.qty').val(1);
                    calculateGrandTotal();
                });
            }

            initSelect2Client();
            initSelect2Vehicle();
            initSelect2Mechanic();

            $('.select2-jasa').each(function() {
                initSelect2Jasa($(this));
            });
            $('.select2-sparepart').each(function() {
                initSelect2Sparepart($(this));
            });

            // Tambah row jasa
            $('#add-jasa').click(function() {
                let key = Date.now();
                let row = `<tr data-key="${key}">
            <td><select name="jobs[${key}][id_jasa]" class="form-control select2-jasa"></select></td>
            <td>
                <input type="text" class="form-control job-price text-end" readonly>
                <input type="hidden" name="jobs[${key}][price]" class="job-price-hidden">
            </td>
            <td><input type="number" name="jobs[${key}][qty]" class="form-control job-qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control job-subtotal text-end" readonly>
                <input type="hidden" name="jobs[${key}][subtotal]" class="job-subtotal-hidden">
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
        </tr>`;
                $('#table-jasa tbody').append(row);
                initSelect2Jasa($('#table-jasa .select2-jasa').last());
            });

            // Tambah row sparepart
            $('#add-sparepart').click(function() {
                let key = Date.now();
                let row = `<tr data-key="${key}">
            <td><select name="spareparts[${key}][id_barang]" class="form-control select2-sparepart"></select></td>
            <td>
                <input type="text" class="form-control price text-end" readonly>
                <input type="hidden" name="spareparts[${key}][price]" class="price-hidden">
                <input type="hidden" name="spareparts[${key}][purchase_price]" class="purchase-price-hidden">
            </td>
            <td><input type="number" name="spareparts[${key}][qty]" class="form-control qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control subtotal text-end" readonly>
                <input type="hidden" name="spareparts[${key}][subtotal]" class="subtotal-hidden">
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
        </tr>`;
                $('#table-sparepart tbody').append(row);
                initSelect2Sparepart($('#table-sparepart .select2-sparepart').last());
            });

            // Hapus row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            // Input qty live update
            $(document).on('input', '.job-qty, .qty', calculateGrandTotal);

            // Kalkulasi awal
            calculateGrandTotal();

            // Submit Ajax
            // $('#form-service').submit(function(e) {
            //     e.preventDefault();
            //     let form = $(this);
            //     let formData = new FormData(this); // <-- pakai FormData

            //     $.ajax({
            //         url: "{{ route('services.update', $service->id) }}",
            //         type: 'POST',
            //         data: formData,
            //         processData: false, // wajib untuk FormData
            //         contentType: false, // wajib untuk FormData
            //         success: function(res) {
            //             Swal.fire('Berhasil', 'Service diperbarui', 'success').then(() => {
            //                 window.location.href = "{{ route('services.index') }}";
            //             });
            //         },
            //         error: function(xhr) {
            //             let msg = 'Terjadi kesalahan';
            //             if (xhr.responseJSON && xhr.responseJSON.errors) {
            //                 msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            //             }
            //             Swal.fire('Gagal', msg, 'error');
            //         }
            //     });
            // });
            $('#form-service').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this); // <-- pakai FormData

                Swal.fire({
                    title: 'Yakin update data?',
                    text: "Perubahan akan disimpan",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('services.update', $service->id) }}",
                            type: 'POST',
                            data: formData,
                            processData: false, // wajib untuk FormData
                            contentType: false, // wajib untuk FormData
                            success: function(res) {
                                if (res.status) {
                                    let msg = res.message;
                                    if (res.warning) {
                                        msg += "<br><b style='color:red'>" + res
                                            .warning + "</b>";
                                    }

                                    Swal.fire({
                                        title: 'Berhasil',
                                        html: msg,
                                        icon: 'success'
                                    }).then(() => {
                                        window.location.href =
                                            "{{ route('services.index') }}";
                                    });
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                let msg = 'Terjadi kesalahan';
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    msg = Object.values(xhr.responseJSON.errors).flat()
                                        .join('<br>');
                                }
                                Swal.fire('Gagal', msg, 'error');
                            }
                        });
                    }
                });
            });

            // document.addEventListener('DOMContentLoaded', function() {
            //     const status = document.getElementById('status_pembayaran');
            //     const dueDateDiv = document.getElementById('due_date_div');
            //     const amountPaidDisplay = document.getElementById('amount_paid_display');
            //     const amountPaid = document.getElementById('amount_paid');
            //     const changeDisplay = document.getElementById('change_display');
            //     const change = document.getElementById('change');

            //     // Tampilkan Jatuh Tempo jika Hutang
            //     status.addEventListener('change', () => {
            //         dueDateDiv.style.display = (status.value === 'hutang') ? 'block' : 'none';
            //     });

            //     // Format angka
            //     amountPaidDisplay.addEventListener('input', function() {
            //         let raw = this.value.replace(/\D/g, '');
            //         this.value = new Intl.NumberFormat('id-ID').format(raw);
            //         amountPaid.value = raw;
            //     });

            //     // Contoh perhitungan otomatis kembalian
            //     amountPaidDisplay.addEventListener('blur', function() {
            //         const total = {{ $service->total_cost ?? 0 }};
            //         const bayar = parseInt(amountPaid.value || 0);
            //         const kembali = bayar - total;
            //         change.value = kembali > 0 ? kembali : 0;
            //         changeDisplay.value = new Intl.NumberFormat('id-ID').format(change.value);
            //     });
            // });

        });
    </script>
@endpush
