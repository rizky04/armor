@extends('layouts.main')

@section('content')
    {{-- <div class="content"> --}}
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Service</h4>
                <h6>Perbarui data service</h6>
            </div>
        </div>

        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card p-3 mb-3">
                <div class="row">
                    <div class="mb-3">
                        <label>Tanggal Service</label>
                        <input type="date" name="service_date"
                            value="{{ \Carbon\Carbon::parse($service->service_date)->format('Y-m-d') }}"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Kategori</label>
                        <input type="text" name="category" value="{{ $service->category }}" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Keluhan</label>
                        <textarea name="complaint" class="form-control">{{ $service->complaint }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-3 mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label>Client</label>
                        <select name="id_client" class="form-control select2-client">
                            <option value="{{ $service->vehicle->client->id_client }}" selected>
                                {{ $service->vehicle->client->nama_client }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Vehicle</label>
                        <select name="id_vehicle" class="form-control select2-vehicle">
                            <option value="{{ $service->vehicle->id }}" selected>
                                {{ $service->vehicle->license_plate }} - {{ $service->vehicle->brand }}
                                {{ $service->vehicle->type }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
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
            {{-- Tabel Jasa --}}
            {{-- ===================== --}}
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5>Pekerjaan / Jasa</h5>
                    <button type="button" id="add-jasa" class="btn btn-sm btn-primary">+ Tambah Jasa</button>
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
                            <tr>
                                <td>
                                    <select name="jobs[{{ $loop->index }}][id_jasa]" class="form-control select2-jasa">
                                        <option value="{{ $job->jasa->id_jasa }}" selected>
                                            {{ $job->jasa->nama_jasa }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control job-price text-end"
                                        value="Rp{{ number_format($job->price, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="jobs[{{ $loop->index }}][price]" class="job-price-hidden"
                                        value="{{ $job->price }}">
                                </td>
                                <td><input type="number" name="jobs[{{ $loop->index }}][qty]"
                                        class="form-control job-qty text-end" value="{{ $job->qty }}" min="1">
                                </td>
                                <td>
                                    <input type="text" class="form-control job-subtotal text-end"
                                        value="Rp{{ number_format($job->subtotal, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="jobs[{{ $loop->index }}][subtotal]"
                                        class="job-subtotal-hidden" value="{{ $job->subtotal }}">
                                </td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ===================== --}}
            {{-- Tabel Sparepart --}}
            {{-- ===================== --}}
            <div class="card p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5>Spareparts</h5>
                    <button type="button" id="add-sparepart" class="btn btn-sm btn-primary">+ Tambah Sparepart</button>
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
                            <tr>
                                <td>
                                    <select name="spareparts[{{ $loop->index }}][id_barang]"
                                        class="form-control select2-sparepart">
                                        <option value="{{ $spare->barang->id_barang }}" selected>
                                            {{ $spare->barang->nama_barang }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control price text-end"
                                        value="Rp{{ number_format($spare->price, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="spareparts[{{ $loop->index }}][price]"
                                        class="price-hidden" value="{{ $spare->price }}">
                                </td>
                                <td><input type="number" name="spareparts[{{ $loop->index }}][qty]"
                                        class="form-control qty text-end" value="{{ $spare->qty }}" min="1"></td>
                                <td>
                                    <input type="text" class="form-control subtotal text-end"
                                        value="Rp{{ number_format($spare->subtotal, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="spareparts[{{ $loop->index }}][subtotal]"
                                        class="subtotal-hidden" value="{{ $spare->subtotal }}">
                                </td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
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

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success">Update Service</button>
                <a href="{{ route('services.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    {{-- </div> --}}
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // ========================
            // Hitung Total
            // ========================
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

            // ========================
            // Init Select2 helper
            // ========================
            function initJobSelect(el) {
                el.select2({
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
                                text: item.kode_barang + ' - ' + item.nama_barang,
                                price: item.harga_jual
                            }))
                        })
                    }
                }).on('select2:select', function(e) {
                    let data = e.params.data;
                    let row = $(this).closest('tr');
                    row.find('.price').val(formatRupiah(data.price));
                    row.find('.price-hidden').val(data.price);
                    row.find('.qty').val(1);
                    calculateGrandTotal();
                });
            }

            // ========================
            // Init awal untuk row yang sudah ada
            // ========================
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

            $('.select2-vehicle').select2({
                placeholder: 'Pilih Vehicle',
                ajax: {
                    url: '{{ route('select2.vehicles') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    })
                }
            });

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

            // job & sparepart yang sudah ada
            $('.select2-jasa').each(function() {
                initJobSelect($(this));
            });
            $('.select2-sparepart').each(function() {
                initSparepartSelect($(this));
            });

            // ========================
            // Tambah Row Jasa
            // ========================
            $('#add-jasa').click(function() {
                let key = Date.now();
                let row = `
        <tr>
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
                initJobSelect($('#table-jasa .select2-jasa').last());
            });

            // ========================
            // Tambah Row Sparepart
            // ========================
            $('#add-sparepart').click(function() {
                let key = Date.now();
                let row = `
        <tr>
            <td><select name="spareparts[${key}][id_barang]" class="form-control select2-sparepart"></select></td>
            <td>
                <input type="text" class="form-control price text-end" readonly>
                <input type="hidden" name="spareparts[${key}][price]" class="price-hidden">
            </td>
            <td><input type="number" name="spareparts[${key}][qty]" class="form-control qty text-end" min="1" value="1"></td>
            <td>
                <input type="text" class="form-control subtotal text-end" readonly>
                <input type="hidden" name="spareparts[${key}][subtotal]" class="subtotal-hidden">
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
        </tr>`;
                $('#table-sparepart tbody').append(row);
                initSparepartSelect($('#table-sparepart .select2-sparepart').last());
            });

            // ========================
            // Event Global
            // ========================
            $(document).on('input', '.job-qty, .qty', calculateGrandTotal);
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            // kalkulasi awal (jika ada data lama)
            calculateGrandTotal();
        });
    </script>
@endpush
