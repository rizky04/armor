@extends('layouts.main')

@section('content')
{{-- <div class="content"> --}}
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Service</h4>
            <h6>Ubah data service kendaraan</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="service-form">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kendaraan (Customer)</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-control">
                                <option value="{{ $service->vehicle->id }}" selected>
                                    {{ $service->vehicle->license_plate }} - {{ $service->vehicle->client->nama_client }}
                                </option>
                            </select>
                            <div class="invalid-feedback" id="error-vehicle_id"></div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Tanggal Service</label>
                            <input type="date" name="service_date" value="{{ $service->service_date }}" class="form-control">
                            {{-- <input type="text" name="service_date" value="{{ $service->service_date }}" class="form-control"> --}}
                            <div class="invalid-feedback" id="error-service_date"></div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-control">
                                <option value="fast service" {{ $service->category == 'fast service' ? 'selected' : '' }}>Fast Service</option>
                                <option value="inap" {{ $service->category == 'inap' ? 'selected' : '' }}>Inap</option>
                            </select>
                            <div class="invalid-feedback" id="error-category"></div>
                        </div>
                    </div>

                    <!-- Keluhan -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Keluhan</label>
                            <textarea name="complaint" class="form-control">{{ $service->complaint }}</textarea>
                            <div class="invalid-feedback" id="error-complaint"></div>
                        </div>
                    </div>

                    <!-- Mekanik -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Mekanik</label>
                            <select name="mechanics[]" class="form-control" multiple>
                                @foreach ($mechanics as $m)
                                    <option value="{{ $m->id }}" {{ in_array($m->id, $service->mechanics->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-mechanics"></div>
                        </div>
                    </div>
                </div>

                <!-- Jobs -->
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <label>Service Jobs</label>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="jobs-table">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Job</th>
                                        <th style="width:20%">Harga</th>
                                        <th style="width:15%">Qty</th>
                                        <th style="width:30%">Subtotal</th>
                                        <th class="text-center" style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($service->jobs as $job)
                                        <tr>
                                            <td>
                                                <select name="jobs[{{ $job->id }}][id]" class="form-control job-select">
                                                    <option value="{{ $job->jasa->id_jasa }}" selected>{{ $job->jasa->nama_jasa }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control job-price text-end" readonly value="{{ number_format($job->jasa->harga_jasa, 0, ',', '.') }}" data-raw="{{ $job->jasa->harga_jasa }}">
                                                <input type="hidden" name="jobs[{{ $job->id }}][price]" class="job-price-hidden" value="{{ $job->jasa->harga_jasa }}">
                                            </td>
                                            <td>
                                                <input type="number" name="jobs[{{ $job->id }}][qty]" class="form-control job-qty text-end" min="1" value="{{ $job->qty }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control job-subtotal text-end" readonly value="{{ number_format($job->subtotal, 0, ',', '.') }}">
                                                <input type="hidden" name="jobs[{{ $job->id }}][subtotal]" class="job-subtotal-hidden" value="{{ $job->subtotal }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                                        <th class="text-center" style="width:5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($service->spareparts as $sp)
                                        <tr>
                                            <td>
                                                <select name="spareparts[{{ $sp->id }}][id]" class="form-control sparepart-select">
                                                    <option value="{{ $sp->barang->id_barang }}" selected>{{ $sp->barang->kode_barang }} - {{ $sp->barang->nama_barang }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control price text-end" readonly value="{{ number_format($sp->barang->harga_jual, 0, ',', '.') }}" data-raw="{{ $sp->barang->harga_jual }}">
                                                <input type="hidden" name="spareparts[{{ $sp->id }}][price]" class="price-hidden" value="{{ $sp->barang->harga_jual }}">
                                            </td>
                                            <td>
                                                <input type="number" name="spareparts[{{ $sp->id }}][qty]" class="form-control qty text-end" min="1" value="{{ $sp->qty }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control subtotal text-end" readonly value="{{ number_format($sp->subtotal, 0, ',', '.') }}">
                                                <input type="hidden" name="spareparts[{{ $sp->id }}][subtotal]" class="subtotal-hidden" value="{{ $sp->subtotal }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm remove-row"><i class="fa-regular fa-square-minus" style="color: red"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                        <button type="submit" class="btn btn-primary me-2">Update</button>
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
$(document).ready(function() {
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function calculateGrandTotal() {
        let totalSpareparts = 0, totalJobs = 0;

        $('#spareparts-table tbody tr').each(function() {
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let price = parseFloat($(this).find('.price').data('raw')) || 0;
            let rowTotal = qty * price;
            $(this).find('.subtotal').val(formatRupiah(rowTotal));
            $(this).find('.subtotal-hidden').val(rowTotal);
            totalSpareparts += rowTotal;
        });

        $('#jobs-table tbody tr').each(function() {
            let qty = parseFloat($(this).find('.job-qty').val()) || 0;
            let price = parseFloat($(this).find('.job-price').data('raw')) || 0;
            let rowTotal = qty * price;
            $(this).find('.job-subtotal').val(formatRupiah(rowTotal));
            $(this).find('.job-subtotal-hidden').val(rowTotal);
            totalJobs += rowTotal;
        });

        $('#total-spareparts').val(formatRupiah(totalSpareparts));
        $('#total-jobs').val(formatRupiah(totalJobs));
        $('#grand-total').val(formatRupiah(totalSpareparts + totalJobs));
    }

    // inisialisasi select2
    function initJobSelect(el) {
        el.select2({
            placeholder: 'Pilih pekerjaan',
            ajax: {
                url: "{{ route('select2.jasa') }}",
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(item => ({ id: item.id_jasa, text: item.nama_jasa, price: item.harga_jasa }))
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
                    results: data.map(item => ({ id: item.id_barang, text: item.kode_barang+' - '+item.nama_barang, price: item.harga_jual }))
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

    // event qty dan remove
    $(document).on('input', '.qty, .job-qty', calculateGrandTotal);
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    // mekanik select2
    $('select[name="mechanics[]"]').select2({ placeholder: "Pilih Mekanik...", width: '100%' });

    // kendaraan select2
    $('#vehicle_id').select2({
        placeholder: 'Pilih kendaraan',
        ajax: {
            url: "{{ route('select2.vehicles') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({ id: item.id, text: item.license_plate+' - '+item.client.nama_client }))
            })
        }
    });

    // hitung ulang total ketika halaman dibuka
    calculateGrandTotal();

    // submit
    $('#service-form').submit(function(e) {
        e.preventDefault();
        let form = $(this);

        Swal.fire({
            title: 'Yakin update data?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('services.update', $service->id) }}",
                    method: "POST",
                    data: form.serialize(),
                    success: function() {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Service berhasil diupdate' })
                        .then(() => window.location.href = "{{ route('services.index') }}");
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
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan server.' });
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
