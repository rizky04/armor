{{-- @extends('layouts.main')

@section('content')
<div class="container">
    <h3>Laporan Kasir</h3>
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
        </div>
        <div class="col-md-3">
            <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" id="btnFilter">Tampilkan</button>
        </div>
    </div>

  <div class="card">
    <div class="card-body">
          <table class="table table-bordered" id="tableKasir">
        <thead class="table-light">
            <tr>
                <th>Nama Kasir</th>
                <th>Total Service</th>
                <th>Total Sales</th>
                <th>Total Omzet</th>
                <th>Detail Transaksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    function loadKasirData() {
        let start = $('#start_date').val();
        let end = $('#end_date').val();

        $.ajax({
            url: "{{ route('laporan.kasir') }}",
            type: 'GET',
            data: { start_date: start, end_date: end },
            beforeSend: function() {
                $('#tableKasir tbody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
            },
            success: function(res) {
                let html = '';

                if (res.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
                } else {
                    res.data.forEach(kasir => {
                        html += `
                            <tr>
                                <td>${kasir.nama_user}</td>
                                <td>${kasir.total_service}</td>
                                <td>${kasir.total_sales}</td>
                                <td>Rp ${kasir.total_omzet.toLocaleString('id-ID')}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-user='${JSON.stringify(kasir)}'>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#tableKasir tbody').html(html);
            },
            error: function(err) {
                alert('Gagal memuat data laporan kasir');
                console.log(err);
            }
        });
    }

    loadKasirData();

    $('#btnFilter').click(function() {
        loadKasirData();
    });

    // === Detail Transaksi Modal ===
    $(document).on('click', '.btn-detail', function() {
        let kasir = $(this).data('user');
        let detailHtml = `
            <h5>Kasir: ${kasir.nama_user}</h5>
            <h6>Service</h6>
            <ul>
                ${kasir.transaksi_service.map(s => `<li>${s.nomor} - ${s.client} - Rp ${s.total}</li>`).join('')}
            </ul>
            <h6>Sales</h6>
            <ul>
                ${kasir.transaksi_sales.map(p => `<li>${p.nomor} - ${p.client} - Rp ${p.total}</li>`).join('')}
            </ul>
        `;
        Swal.fire({
            title: 'Detail Transaksi',
            html: detailHtml,
            width: 700,
            confirmButtonText: 'Tutup'
        });
    });

});
</script>
@endpush --}}

@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Report Kasir</h4>
            <h6>Pencatatan Service dan penjualan oleh kasir</h6>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" id="btnFilter">Tampilkan</button>
                </div>
            </div>

            <table class="table table-bordered" id="tableKasir">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kasir</th>
                        <th>Total Service</th>
                        <th>Total Sales</th>
                        <th>Total Omzet</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function loadKasirData() {
                let start = $('#start_date').val();
                let end = $('#end_date').val();

                $.ajax({
                    url: "{{ route('laporan.kasir') }}",
                    type: 'GET',
                    data: {
                        start_date: start,
                        end_date: end
                    },
                    beforeSend: function() {
                        $('#tableKasir tbody').html(
                            '<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                    },
                    success: function(res) {
                        let html = '';

                        if (res.data.length === 0) {
                            html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
                        } else {
                            res.data.forEach((kasir, i) => {
                                html += `
                            <tr class="main-row" data-index="${i}">
                                <td>${kasir.nama_user}</td>
                                <td>${kasir.total_service}</td>
                                <td>${kasir.total_sales}</td>
                                <td>Rp ${kasir.total_omzet.toLocaleString('id-ID')}</td>
                                <td><button class="btn btn-sm btn-info btn-detail" data-index="${i}">Lihat Detail</button></td>
                            </tr>
                            <tr class="detail-row" id="detail-${i}" style="display:none;">
                                <td colspan="5" style="background:#f8f9fa;">
                                    <div class="p-2">
                                        <h6 class="mb-1 text-primary">Transaksi Service</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No. Service</th>
                                                    <th>Tanggal</th>
                                                    <th>Client</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${kasir.transaksi_service.length > 0 ?
                                                    kasir.transaksi_service.map(s => `
                                                            <tr>
                                                                <td>${s.nomor}</td>
                                                                <td>${s.tanggal}</td>
                                                                <td>${s.client}</td>
                                                                <td>Rp ${s.total}</td>
                                                            </tr>
                                                        `).join('') :
                                                    `<tr><td colspan="4" class="text-center">Tidak ada transaksi service</td></tr>`
                                                }
                                            </tbody>
                                        </table>

                                        <h6 class="mb-1 text-success">Transaksi Sales</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No. Sales</th>
                                                    <th>Tanggal</th>
                                                    <th>Client</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${kasir.transaksi_sales.length > 0 ?
                                                    kasir.transaksi_sales.map(p => `
                                                            <tr>
                                                                <td>${p.nomor}</td>
                                                                <td>${p.tanggal}</td>
                                                                <td>${p.client}</td>
                                                                <td>Rp ${p.total}</td>
                                                            </tr>
                                                        `).join('') :
                                                    `<tr><td colspan="4" class="text-center">Tidak ada transaksi sales</td></tr>`
                                                }
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        `;
                            });
                        }

                        $('#tableKasir tbody').html(html);
                    },
                    error: function(err) {
                        alert('Gagal memuat data laporan kasir');
                        console.log(err);
                    }
                });
            }

            loadKasirData();

            $('#btnFilter').click(function() {
                loadKasirData();
            });

            // Toggle detail row
            $(document).on('click', '.btn-detail', function() {
                const index = $(this).data('index');
                const detailRow = $(`#detail-${index}`);
                detailRow.toggle();
                $(this).text(detailRow.is(':visible') ? 'Tutup' : 'Lihat Detail');
            });

        });
    </script>
@endpush
