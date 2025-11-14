@extends('layouts.main')

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Create Service</h4>
                <h6>Tambah data service kendaraan</h6>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="service-form">
                    @csrf
                    <div class="row">
                        <!-- Customer (Vehicle) -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Kendaraan (Customer)</label>
                                <select id="vehicle_id" name="vehicle_id" class="form-control"></select>
                                <div class="invalid-feedback" id="error-vehicle_id"></div>
                            </div>
                        </div>

                        <!-- Service Date -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Tanggal Service</label>
                                <input type="date" name="service_date" class="form-control">
                                <div class="invalid-feedback" id="error-service_date"></div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category" class="form-control">
                                    <option value="fast service">Fast Service</option>
                                    <option value="inap">Inap</option>
                                </select>
                                <div class="invalid-feedback" id="error-category"></div>
                            </div>
                        </div>

                        <!-- Complaint -->
                        <div class="col-lg-12 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Keluhan</label>
                                <textarea name="complaint" class="form-control"></textarea>
                                <div class="invalid-feedback" id="error-complaint"></div>
                            </div>
                        </div>

                        <!-- Mechanics -->
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Mekanik</label>
                                <select name="mechanics[]" class="form-control" multiple>
                                    @foreach ($mechanics as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-mechanics"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Jobs Table -->
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <label>Service Jobs</label>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="jobs-table">
                                    <thead>
                                        <tr>
                                            <th>Job Name</th>
                                            <th style="width:50px;text-align:center;">
                                                <button type="button" id="add-job"
                                                    class="btn btn-sm btn-success">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="invalid-feedback" id="error-jobs"></div>
                        </div>
                    </div>

                    <!-- Spareparts Table -->
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <label>Spareparts</label>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="spareparts-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:35%">Sparepart</th>
                                            <th style="width:20%">Harga</th>
                                            <th style="width:15%">Qty</th>
                                            <th style="width:30%">Total</th>
                                            <th style="width:5%; text-align:center;">
                                                <button type="button" id="add-sparepart"
                                                    class="btn btn-sm btn-success">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Grand Total</th>
                                            <th>
                                                <input type="text" id="grand-total" class="form-control text-end fw-bold"
                                                    readonly>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                            <div class="invalid-feedback" id="error-spareparts"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary me-2">Save</button>
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // ===============================
                // Helper format Rupiah
                // ===============================
                function formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(angka);
                }

                // ===============================
                // Recalculate Grand Total
                // ===============================
                function calculateGrandTotal() {
                    let total = 0;
                    $('#spareparts-table tbody tr').each(function() {
                        let qty = parseFloat($(this).find('.qty').val()) || 0;
                        let price = parseFloat($(this).find('.price').val()) || 0;
                        let rowTotal = qty * price;
                        $(this).find('.total').val(formatRupiah(rowTotal));
                        total += rowTotal;
                    });
                    $('#grand-total').val(formatRupiah(total));
                }

                // ===============================
                // Select2 Kendaraan (Customer)
                // ===============================
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
                                text: item.license_plate + ' - ' + item.client.nama_client
                            }))
                        })
                    }
                });

                // ===============================
                // Tambah Job Row
                // ===============================
                $('#add-job').click(function() {
                    $('#jobs-table tbody').append(`
            <tr>
                <td>
                    <select name="jobs[]" class="form-control job-select" style="width:100%"></select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row">x</button>
                </td>
            </tr>
        `);

                    $('.job-select').last().select2({
                        placeholder: 'Pilih pekerjaan',
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
                                    text: item.nama_jasa
                                }))
                            })
                        }
                    });
                });

                $('#add-sparepart').click(function() {
                    $('#spareparts-table tbody').append(`
                            <tr>
                                <td>
                                    <select name="spareparts[]" class="form-control sparepart-select" style="width:100%"></select>
                                </td>
                                <td>
                                    <input type="text" class="form-control price text-end" name="prices[]" value="0" readonly>
                                </td>
                                <td style="width:80px">
                                    <input type="number" class="form-control qty text-center" name="quantities[]" value="1" min="1">
                                </td>
                                <td>
                                    <input type="text" class="form-control total text-end" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">x</button>
                                </td>
                            </tr>
                        `);

                    $('.sparepart-select').last().select2({
                        placeholder: 'Pilih sparepart',
                        ajax: {
                            url: "{{ route('select2.products') }}",
                            dataType: 'json',
                            delay: 250,
                            data: params => ({
                                q: params.term
                            }),
                            processResults: data => ({
                                results: data.map(item => ({
                                    id: item.id_barang,
                                    text: item.kode_barang + ' - ' + item
                                        .nama_barang,
                                    price: item.harga_jual
                                }))
                            })
                        }
                    }).on('select2:select', function(e) {
                        let data = e.params.data;
                        let row = $(this).closest('tr');
                        row.find('.price').val(data.price);
                        row.find('.qty').val(1);
                        calculateGrandTotal();
                    });
                });


                // ===============================
                // Event Listener (Qty & Price Change)
                // ===============================
                $(document).on('input', '.qty, .price', function() {
                    calculateGrandTotal();
                });

                // ===============================
                // Hapus Row
                // ===============================
                $(document).on('click', '.remove-row', function() {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                });

                // ===============================
                // Submit Form
                // ===============================
                $('#service-form').submit(function(e) {
                    e.preventDefault();
                    let formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('services.store') }}",
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            alert('Service berhasil disimpan!');
                            window.location.href = "{{ route('services.index') }}";
                        },
                        error: function(xhr) {
                            $('.invalid-feedback').text('');
                            $('.form-control').removeClass('is-invalid');

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $(`[name="${key}"]`).addClass('is-invalid');
                                    $(`#error-${key}`).text(value[0]);
                                });
                            } else {
                                alert('Terjadi kesalahan!');
                            }
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
