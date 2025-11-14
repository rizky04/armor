@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Riwayat Stok Opname</h4>
        <h6>Histori perubahan stok barang</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Cari nama atau kode barang...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="log-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Kode Sistem</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Keterangan</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <nav class="mt-3">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let searchQuery = '';

function loadLogs(page = 1, search = '') {
    $('#log-table tbody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
    $.get("{{ route('stok-opname.logs.data') }}", { page: page, search: search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        if (res.data.length === 0) {
            rows = `<tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>`;
        } else {
            res.data.forEach(l => {
                rows += `
                    <tr>
                        <td>${++i}</td>
                        <td>${new Date(l.tanggal).toLocaleString()}</td>
                        <td>${l.stok_sistem}</td>
                        <td>${l.stok_fisik}</td>
                        <td class="${l.selisih > 0 ? 'text-success' : l.selisih < 0 ? 'text-danger' : ''}">
                            ${l.selisih}
                        </td>
                        <td>${l.barang?.id_barang ?? '-'}</td>
                        <td>${l.barang?.kode_barang ?? '-'}</td>
                        <td>${l.barang?.nama_barang ?? '-'}</td>
                        <td>${l.barang?.merk ?? '-'}</td>
                        <td>${l.barang?.keterangan ?? '-'}</td>
                        <td>${l.barang?.jenis ?? '-'}</td>

                    </tr>`;
            });
        }
        $('#log-table tbody').html(rows);

        // Pagination
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadLogs(${current - 1}, searchQuery)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadLogs(${i}, searchQuery)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadLogs(${current + 1}, searchQuery)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadLogs();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadLogs(1, searchQuery);
    });
});
</script>
@endpush
