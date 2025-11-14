@extends('layouts.main')

@section('content')
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
            <div class="mb-3">
                <label>Kendaraan (Customer)</label>
                <select id="vehicle_id" name="vehicle_id" class="form-control"></select>
                <div class="invalid-feedback" id="error-vehicle_id"></div>
            </div>

            <div class="mb-3">
                <label>Service Date</label>
                <input type="date" name="service_date" class="form-control">
                <div class="invalid-feedback" id="error-service_date"></div>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="fast service">Fast Service</option>
                    <option value="inap">Inap</option>
                </select>
                <div class="invalid-feedback" id="error-category"></div>
            </div>

            <div class="mb-3">
                <label>Complaint</label>
                <textarea name="complaint" class="form-control"></textarea>
                <div class="invalid-feedback" id="error-complaint"></div>
            </div>

            <!-- Multiple Mechanic -->
            <div class="mb-3">
                <label>Mechanics</label>
                <select name="mechanics[]" class="form-control" multiple>
                    @foreach($mechanics as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback" id="error-mechanics"></div>
            </div>

            <!-- Jobs -->
            <div class="mb-3">
                <label>Service Jobs</label>
                <table class="table table-bordered" id="jobs-table">
                    <thead>
                        <tr>
                            <th>Job Name</th>
                            <th style="width: 50px; text-align: center;">
                                <button type="button" id="add-job" class="btn btn-sm btn-success" title="Tambah Job">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="invalid-feedback" id="error-jobs"></div>
            </div>

            <!-- Spareparts -->
            <div class="mb-3">
                <label>Spareparts</label>
                <table class="table table-bordered" id="spareparts-table">
                    <thead>
                        <tr>
                            <th>Sparepart</th>
                            <th>Harga</th>
                            <th style="width: 80px;">Qty</th>
                            <th>Total</th>
                            <th style="width: 50px; text-align: center;">
                                <button type="button" id="add-sparepart" class="btn btn-sm btn-success" title="Tambah Sparepart">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Grand Total</th>
                            <th>
                                <input type="text" id="grand-total" class="form-control" readonly>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <div class="invalid-feedback" id="error-spareparts"></div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
}
.invalid-feedback {
    display: block;
    color: #dc3545;
}
.invalid-feedback-array {
    display: block;
    font-size: 80%;
    color: #dc3545;
}
#jobs-table button.remove-row,
#spareparts-table button.remove-row {
    display: block;
    margin: auto;
}
</style>

<script>
$(document).ready(function(){
    let jobIndex = 0;
    let spareIndex = 0;

    // Format angka ke Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    // Hitung total per baris
    function hitungTotalRow($row){
        let harga = parseFloat($row.find('.harga-raw').val()) || 0;
        let qty   = parseInt($row.find('.qty').val()) || 1;
        let total = harga * qty;

        $row.find('.total-display').val(formatRupiah(total));
        $row.find('.total-raw').val(total);

        hitungGrandTotal();
    }

    // Hitung Grand Total semua baris
    function hitungGrandTotal(){
        let grandTotal = 0;
        $('#spareparts-table tbody tr').each(function(){
            grandTotal += parseFloat($(this).find('.total-raw').val()) || 0;
        });
        $('#grand-total').val(formatRupiah(grandTotal));
    }

    // Select2 Vehicle
    $('#vehicle_id').select2({
        placeholder: "-- pilih kendaraan --",
        allowClear: true,
        width: '100%',
        ajax: {
            url: "{{ route('vehicles.get') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            }
        }
    });

    // Add Job
    $('#add-job').click(function(){
        $('#jobs-table tbody').append(`
            <tr>
                <td><input type="text" name="jobs[${jobIndex}][job_name]" class="form-control"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
            </tr>
        `);
        jobIndex++;
    });

    // Add Sparepart
    $('#add-sparepart').click(function(){
        let row = `
            <tr>
                <td>
                    <select name="spareparts[${spareIndex}][sparepart_id]" class="form-control sparepart-select"></select>
                </td>
                <td>
                    <input type="text" class="form-control harga-display" readonly>
                    <input type="hidden" name="spareparts[${spareIndex}][harga_jual]" class="harga-raw">
                </td>
                <td>
                    <input type="number" name="spareparts[${spareIndex}][quantity]" class="form-control qty" value="1" min="1">
                </td>
                <td>
                    <input type="text" class="form-control total-display" readonly>
                    <input type="hidden" name="spareparts[${spareIndex}][total]" class="total-raw">
                </td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
            </tr>
        `;
        $('#spareparts-table tbody').append(row);

        // init select2 sparepart
        $(`.sparepart-select`).last().select2({
            placeholder: "Cari sparepart...",
            ajax: {
                url: "{{ route('select2.products') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id_barang,
                                text: item.nama_barang + ' - ' + (item.kode_barang ?? '') + ' - ' + (item.merk_barang ?? '') + ' - ' + (item.harga_jual ?? ''),
                                harga_jual: item.harga_jual
                            }
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            let data = e.params.data;
            let $row = $(this).closest('tr');

            $row.find('.harga-raw').val(data.harga_jual);
            $row.find('.harga-display').val(formatRupiah(data.harga_jual));

            hitungTotalRow($row);
        });

        spareIndex++;
    });

    // Qty change
    $(document).on('input', '.qty', function(){
        let $row = $(this).closest('tr');
        hitungTotalRow($row);
    });

    // Remove row
    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
        hitungGrandTotal();
    });

    $('select[name="mechanics[]"]').select2({
        placeholder: "Pilih Mekanik...",
        width: '100%'
    });

    // Submit form AJAX
    $('#service-form').on('submit', function(e){
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        $.ajax({
            url: "{{ route('services.store') }}",
            type: "POST",
            data: formData,
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Service berhasil disimpan!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('services.index') }}";
                });
            },
            error: function(xhr){
                Swal.close();
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value){
                        let errorMessage = value[0];
                        if (key.includes('.')) {
                            let parts = key.split('.');
                            let baseKey = parts[0];
                            let index = parts[1];
                            let field = parts[2];
                            if (baseKey === 'jobs') {
                                let row = $('#jobs-table tbody tr').eq(index);
                                let input = row.find(`[name$="[${field}]"]`);
                                input.addClass('is-invalid');
                            } else if (baseKey === 'spareparts') {
                                let row = $('#spareparts-table tbody tr').eq(index);
                                let input = row.find(`[name$="[${field}]"]`);
                                input.addClass('is-invalid');
                                if (field === 'sparepart_id') {
                                    row.find('td:first').append(`<div class="invalid-feedback-array">${errorMessage}</div>`);
                                }
                            }
                        } else {
                            let input = form.find(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            $(`#error-${key}`).text(errorMessage);
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan periksa inputan Anda di form, terutama bagian tabel Jobs dan Spareparts.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                    });
                }
            }
        });
    });
});
</script>
@endpush
