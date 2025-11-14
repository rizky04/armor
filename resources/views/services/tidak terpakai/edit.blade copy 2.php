@extends('layouts.main')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Edit Service</h4>
            <h6>Ubah data service kendaraan</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="service-form" method="POST" action="{{ route('services.update', $service->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Kendaraan -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Kendaraan</label>
                            <select id="vehicle_id" name="vehicle_id" class="form-control"></select>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Tanggal Service</label>
                            <input type="date" name="service_date" value="{{ $service->service_date }}" class="form-control">
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-control">
                                <option value="fast service" {{ $service->category == 'fast service' ? 'selected' : '' }}>Fast Service</option>
                                <option value="inap" {{ $service->category == 'inap' ? 'selected' : '' }}>Inap</option>
                            </select>
                        </div>
                    </div>

                    <!-- Keluhan -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Keluhan</label>
                            <textarea name="complaint" class="form-control">{{ $service->complaint }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Jobs -->
                <div class="mt-3">
                    <label>Jobs</label>
                    <table class="table table-bordered" id="jobs-table">
                        <thead>
                            <tr>
                                <th style="width:35%">Job</th>
                                <th style="width:20%">Harga</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:25%">Subtotal</th>
                                <th style="width:5%">
                                    <button type="button" id="add-job" class="btn btn-sm btn-success">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->jobs as $job)
                            <tr>
                                <td>
                                    <select name="jobs[{{ $job->id }}][id]" class="form-control job-select">
                                        <option value="{{ $job->jasa->id_jasa }}" selected>
                                            {{ $job->jasa->nama_jasa }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control job-price text-end" value="{{ number_format($job->jasa->harga_jasa,0,',','.') }}" readonly data-raw="{{ $job->jasa->harga_jasa }}">
                                </td>
                                <td>
                                    <input type="number" name="jobs[{{ $job->id }}][qty]" class="form-control job-qty text-end" value="{{ $job->qty }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control job-subtotal text-end" value="{{ number_format($job->subtotal,0,',','.') }}" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-minus"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Spareparts -->
                <div class="mt-3">
                    <label>Spareparts</label>
                    <table class="table table-bordered" id="spareparts-table">
                        <thead>
                            <tr>
                                <th style="width:35%">Sparepart</th>
                                <th style="width:20%">Harga</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:25%">Subtotal</th>
                                <th style="width:5%">
                                    <button type="button" id="add-sparepart" class="btn btn-sm btn-success">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->spareparts as $sp)
                            <tr>
                                <td>
                                    <select name="spareparts[{{ $sp->id }}][id]" class="form-control sparepart-select">
                                        <option value="{{ $sp->barang->id_barang }}" selected>
                                            {{ $sp->barang->kode_barang }} - {{ $sp->barang->nama_barang }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control price text-end" value="{{ number_format($sp->barang->harga_jual,0,',','.') }}" readonly data-raw="{{ $sp->barang->harga_jual }}">
                                </td>
                                <td>
                                    <input type="number" name="spareparts[{{ $sp->id }}][qty]" class="form-control qty text-end" value="{{ $sp->qty }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal text-end" value="{{ number_format($sp->subtotal,0,',','.') }}" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-minus"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="mt-3">
                    <table class="table table-bordered">
                        <tr>
                            <th class="text-end">Total Jasa</th>
                            <td><input type="text" id="total-jobs" class="form-control text-end fw-bold" readonly></td>
                        </tr>
                        <tr>
                            <th class="text-end">Total Spareparts</th>
                            <td><input type="text" id="total-spareparts" class="form-control text-end fw-bold" readonly></td>
                        </tr>
                        <tr>
                            <th class="text-end bg-light">Grand Total</th>
                            <td><input type="text" id="grand-total" class="form-control text-end fw-bold bg-light" readonly></td>
                        </tr>
                    </table>
                </div>

                <!-- Submit -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka || 0);
    }

    // hitung ulang semua total
    function recalcTotals() {
        let totalJobs = 0;
        let totalSpareparts = 0;

        $('#jobs-table tbody tr').each(function(){
            let price = parseInt($(this).find('.job-price').data('raw')) || 0;
            let qty = parseInt($(this).find('.job-qty').val()) || 0;
            let subtotal = price * qty;
            $(this).find('.job-subtotal').val(formatRupiah(subtotal));
            totalJobs += subtotal;
        });

        $('#spareparts-table tbody tr').each(function(){
            let price = parseInt($(this).find('.price').data('raw')) || 0;
            let qty = parseInt($(this).find('.qty').val()) || 0;
            let subtotal = price * qty;
            $(this).find('.subtotal').val(formatRupiah(subtotal));
            totalSpareparts += subtotal;
        });

        $('#total-jobs').val(formatRupiah(totalJobs));
        $('#total-spareparts').val(formatRupiah(totalSpareparts));
        $('#grand-total').val(formatRupiah(totalJobs + totalSpareparts));
    }

    // select2 init reusable
    function initJobSelect(el) {
        el.select2({
            ajax: {
                url: '{{ route("select2.jasa") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            }
        }).on('select2:select', function (e) {
            let price = e.params.data.harga || 0;
            let row = $(this).closest('tr');
            row.find('.job-price').val(formatRupiah(price)).data('raw', price);
            row.find('.job-qty').val(1);
            recalcTotals();
        });
    }

    function initSparepartSelect(el) {
        el.select2({
            ajax: {
                url: '{{ route("select2.barang") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            }
        }).on('select2:select', function (e) {
            let price = e.params.data.harga || 0;
            let row = $(this).closest('tr');
            row.find('.price').val(formatRupiah(price)).data('raw', price);
            row.find('.qty').val(1);
            recalcTotals();
        });
    }

    // init untuk data existing
    $('.job-select').each(function(){ initJobSelect($(this)); });
    $('.sparepart-select').each(function(){ initSparepartSelect($(this)); });

    // tambah row job
    $('#add-job').click(function(){
        let row = `
        <tr>
            <td><select name="jobs[new][id][]" class="form-control job-select"></select></td>
            <td><input type="text" class="form-control job-price text-end" readonly data-raw="0"></td>
            <td><input type="number" name="jobs[new][qty][]" class="form-control job-qty text-end" value="1"></td>
            <td><input type="text" class="form-control job-subtotal text-end" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-minus"></i></button></td>
        </tr>`;
        $('#jobs-table tbody').append(row);
        initJobSelect($('#jobs-table tbody tr:last .job-select'));
    });

    // tambah row sparepart
    $('#add-sparepart').click(function(){
        let row = `
        <tr>
            <td><select name="spareparts[new][id][]" class="form-control sparepart-select"></select></td>
            <td><input type="text" class="form-control price text-end" readonly data-raw="0"></td>
            <td><input type="number" name="spareparts[new][qty][]" class="form-control qty text-end" value="1"></td>
            <td><input type="text" class="form-control subtotal text-end" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-minus"></i></button></td>
        </tr>`;
        $('#spareparts-table tbody').append(row);
        initSparepartSelect($('#spareparts-table tbody tr:last .sparepart-select'));
    });

    // hapus row
    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
        recalcTotals();
    });

    // qty berubah
    $(document).on('input', '.job-qty, .qty', function(){
        recalcTotals();
    });

    // hitung awal
    recalcTotals();
});
</script>
@endpush
