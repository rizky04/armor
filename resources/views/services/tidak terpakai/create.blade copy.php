@extends('layouts.main')

@section('content')
{{-- <div class="content"> --}}
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
                    <!-- Kendaraan -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kendaraan (Customer)</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-control"></select>
                            <div class="invalid-feedback" id="error-vehicle_id"></div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Tanggal Service</label>
                            <input type="date" name="service_date" class="form-control">
                            <div class="invalid-feedback" id="error-service_date"></div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-control">
                                <option value="fast service">Fast Service</option>
                                <option value="inap">Inap</option>
                            </select>
                            <div class="invalid-feedback" id="error-category"></div>
                        </div>
                    </div>

                    <!-- Keluhan -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Keluhan</label>
                            <textarea name="complaint" class="form-control"></textarea>
                            <div class="invalid-feedback" id="error-complaint"></div>
                        </div>
                    </div>

                    <!-- Mekanik -->
                    <div class="col-lg-12">
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
                                        <td><input type="text" id="total-jobs" class="form-control text-end fw-bold" readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end">Total Sparepart</th>
                                        <td><input type="text" id="total-spareparts" class="form-control text-end fw-bold" readonly></td>
                                    </tr>
                                    <tr>
                                        <th class="text-end bg-light">Grand Total</th>
                                        <td><input type="text" id="grand-total" name="grand_total" class="form-control text-end fw-bold bg-light" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="invalid-feedback" id="error-spareparts"></div>
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
        let totalSpareparts = 0, totalJobs = 0;

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

    // ============ Select2 Init ============
    function initJobSelect(el) {
        el.select2({
            placeholder: 'Pilih pekerjaan',
            ajax: {
                url: "{{ route('select2.jasa') }}",
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
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
            row.find('.job-price').val(formatRupiah(data.price)).data('raw', data.price);
            row.find('.job-price-hidden').val(data.price);
            row.find('.job-qty').val(1);
            calculateGrandTotal();
        });
    }

    function initSparepartSelect(el) {
        el.select2({
            placeholder: 'Pilih sparepart',
            ajax: {
                url: "{{ route('select2.barang') }}",
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.id_barang,
                        text: item.kode_barang + ' - ' + item.nama_barang,
                        price: item.harga_jual
                    }))
                })
            }
        }).on('select2:select', function(e) {
            let data = e.params.data;
            let row = $(this).closest('tr');
            row.find('.price').val(formatRupiah(data.price)).data('raw', data.price);
            row.find('.price-hidden').val(data.price);
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
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id,
                    text: item.license_plate + ' - ' + item.client.nama_client
                }))
            })
        }
    });

      $('select[name="mechanics[]"]').select2({
        placeholder: "Pilih Mekanik...",
        width: '100%'
    });

    // ============ Submit Form ============
    // $('#service-form').submit(function(e) {
    //     e.preventDefault();
    //     let form = $(this);

    //     Swal.fire({
    //         title: 'Yakin simpan data?',
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonText: 'Ya, Simpan',
    //         cancelButtonText: 'Batal'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                 url: "{{ route('services.store') }}",
    //                 method: "POST",
    //                 data: form.serialize(),
    //                 success: function() {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: 'Berhasil!',
    //                         text: 'Service berhasil disimpan'
    //                     }).then(() => {
    //                         window.location.href = "{{ route('services.index') }}";
    //                     });
    //                 },
    //                 error: function(xhr) {
    //                     $('.invalid-feedback').text('');
    //                     $('.form-control').removeClass('is-invalid');
    //                     if (xhr.status === 422) {
    //                         $.each(xhr.responseJSON.errors, function(key, value) {
    //                             $(`[name="${key}"]`).addClass('is-invalid');
    //                             $(`#error-${key}`).text(value[0]);
    //                         });
    //                     } else {
    //                         Swal.fire({
    //                             icon: 'error',
    //                             title: 'Gagal!',
    //                             text: 'Terjadi kesalahan server.'
    //                         });
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // });

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
                            msg += "<br><b style='color:red'>" + res.warning + "</b>";
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: msg
                        }).then(() => {
                            window.location.href = "{{ route('services.index') }}";
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
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`[name="${key}"]`).addClass('is-invalid');
                            $(`#error-${key}`).text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan server.'
                        });
                    }
                }
            });
        }
    });
});

});
</script>
@endpush
