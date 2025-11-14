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

            {{-- <!-- Jobs -->
            <div class="mb-3">
                <label>Service Jobs</label>
                <table class="table" id="jobs-table">
                    <thead>
                        <tr>
                            <th>Job Name</th>
                            <th><button type="button" id="add-job" class="btn btn-sm btn-success">+</button></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="invalid-feedback" id="error-jobs"></div>
            </div>

            <!-- Spareparts -->
            <div class="mb-3">
                <label>Spareparts</label>
                <table class="table" id="spareparts-table">
                    <thead>
                        <tr>
                            <th>Sparepart</th>
                            <th>Qty</th>
                            <th><button type="button" id="add-sparepart" class="btn btn-sm btn-success">+</button></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="invalid-feedback" id="error-spareparts"></div>
            </div> --}}

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
                <th style="width: 50px; text-align: center;">
                    <button type="button" id="add-sparepart" class="btn btn-sm btn-success" title="Tambah Sparepart">+</button>
                </th>
            </tr>
        </thead>
        <tbody></tbody>
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
    font-size: 80%; /* lebih kecil dari font standar */
    color: #dc3545;
}
/* Tambahkan ini ke bagian <style> */
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
                <td><input type="number" name="spareparts[${spareIndex}][quantity]" class="form-control" value="1"></td>
                <td><input type="number" name="spareparts[${spareIndex}][harga_jual]" class="form-control" value="harga jual"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
            </tr>
            <tr>
            <td><input type="number" name="spareparts[${spareIndex}][harga_jual]" class="form-control" value="harga jual"></td>
                <tr>
        `;
        $('#spareparts-table tbody').append(row);

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
                                text: item.nama_barang + ' - ' + (item.kode_barang ?? '') + ' - ' + (item.merk_barang ?? '') ' - ' + (item.harga_jual ?? '')
                            }
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) { return markup; }
        });

        spareIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
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

                // Hapus semua error sebelumnya
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                // Loop melalui setiap error yang dikirim oleh Laravel
                $.each(errors, function(key, value){
                    let errorMessage = value[0];

                    // Cek jika error adalah field array (jobs.0.job_name atau spareparts.1.quantity)
                    if (key.includes('.')) {
                        let parts = key.split('.');
                        let baseKey = parts[0]; // jobs atau spareparts
                        let index = parts[1];   // index baris (0, 1, 2, ...)
                        let field = parts[2];   // job_name atau quantity/sparepart_id

                        // Kasus untuk JOB (menargetkan input di dalam tabel jobs)
                        if (baseKey === 'jobs') {
                            // Mencari baris ke-`index` dan kolom input dengan nama field yang sesuai
                            let row = $('#jobs-table tbody tr').eq(index);
                            let input = row.find(`[name$="[${field}]"]`); // Menggunakan attribute ends with selector

                            input.addClass('is-invalid');
                            // Tidak ada invalid-feedback terpisah di Jobs/Spareparts, jadi kita bisa set title
                            // atau cukup tandai inputnya.
                        }

                        // Kasus untuk SPAREPART (menargetkan input di dalam tabel spareparts)
                        else if (baseKey === 'spareparts') {
                            let row = $('#spareparts-table tbody tr').eq(index);
                            let input;

                            // Jika error pada sparepart_id, target select2-nya
                            if (field === 'sparepart_id') {
                                input = row.find('select');
                            } else {
                                // Jika error pada quantity
                                input = row.find(`[name$="[${field}]"]`);
                            }

                            input.addClass('is-invalid');
                            // Untuk Select2, kita mungkin perlu menambahkan error message di dekatnya
                            if (field === 'sparepart_id') {
                                row.find('td:first').append(`<div class="invalid-feedback-array">${errorMessage}</div>`);
                            }
                        }
                    } else {
                        // Kasus untuk field tunggal (vehicle_id, service_date, category, complaint)
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
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server. Cek konsol.'
                });
            }
        }
        });
    });
});
</script>
@endpush
