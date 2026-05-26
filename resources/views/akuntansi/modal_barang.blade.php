@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Modal Barang</h4>
        <h6>Rekap Stok × Harga Kulak per Barang</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-success no-print" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>
    </div>
</div>

<!-- FILTER -->
<div class="card no-print mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Cari Barang</label>
                <input type="text" id="searchBarang" class="form-control" placeholder="Nama, kode, merk...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter Jenis</label>
                <select id="filterJenis" class="form-select">
                    <option value="">Semua Jenis</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="btnTampilkan">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" id="btnReset">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Total Modal Semua Barang</div>
                        <div class="fs-4 fw-bold text-warning" id="cModalSemua">Rp 0</div>
                        <div class="text-muted small mt-1">Seluruh stok aktif</div>
                    </div>
                    <i class="fa-solid fa-boxes-stacked fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Total Modal (Filter Aktif)</div>
                        <div class="fs-4 fw-bold text-primary" id="cModalFilter">Rp 0</div>
                        <div class="text-muted small mt-1" id="cJumlahItem">0 jenis barang</div>
                    </div>
                    <i class="fa-solid fa-filter fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small mb-1">Halaman Ini</div>
                        <div class="fs-4 fw-bold text-success" id="cModalHalaman">Rp 0</div>
                        <div class="text-muted small mt-1" id="cJumlahHalaman">0 item</div>
                    </div>
                    <i class="fa-solid fa-file-lines fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="tabelModal">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>#</th>
                        <th class="text-start">Kode</th>
                        <th class="text-start">Nama Barang</th>
                        <th class="text-start">Merk</th>
                        <th class="text-start">Jenis</th>
                        <th>Stok</th>
                        <th>Harga Kulak</th>
                        <th>Modal (Stok × Kulak)</th>
                    </tr>
                </thead>
                <tbody id="bodyModal">
                    <tr><td colspan="8" class="text-center text-muted py-4">Klik Tampilkan untuk memuat data.</td></tr>
                </tbody>
                <tfoot>
                    <tr class="table-warning fw-bold text-center">
                        <td colspan="7" class="text-end pe-3">Total Modal Halaman Ini</td>
                        <td id="footerModal">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center px-3 py-2">
            <small class="text-muted" id="infoHalaman"></small>
            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @media print {
        .no-print { display: none !important; }
        .page-btn { display: none !important; }
    }
</style>
<script>
let currentPage = 1;
let currentSearch = '';
let currentJenis = '';

function fmt(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
}

function loadData(page = 1) {
    currentPage = page;
    $('#bodyModal').html('<tr><td colspan="8" class="text-center py-3"><i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat...</td></tr>');

    $.get("{{ route('akuntansi.modal-barang.data') }}", {
        page:   page,
        search: currentSearch,
        jenis:  currentJenis,
    }, function (res) {
        // Isi dropdown jenis (sekali)
        if ($('#filterJenis option').length <= 1 && res.jenis_list) {
            res.jenis_list.forEach(j => {
                $('#filterJenis').append(`<option value="${j}">${j}</option>`);
            });
        }

        // Summary cards
        $('#cModalSemua').text(fmt(res.total_modal_semua));
        $('#cModalFilter').text(fmt(res.total_modal_filter));
        $('#cJumlahItem').text(res.total + ' jenis barang');

        // Baris tabel
        let rows = '';
        let modalHalaman = 0;
        const start = (res.current_page - 1) * res.per_page;

        res.data.forEach((b, i) => {
            const modal = parseFloat(b.modal) || 0;
            modalHalaman += modal;
            rows += `
                <tr>
                    <td class="text-center">${start + i + 1}</td>
                    <td>${b.kode_barang ?? '-'}</td>
                    <td>${b.nama_barang}</td>
                    <td>${b.merk_barang ?? '-'}</td>
                    <td>${b.jenis ?? '-'}</td>
                    <td class="text-center">${b.stok_barang}</td>
                    <td class="text-end">${fmt(b.harga_kulak)}</td>
                    <td class="text-end fw-semibold text-warning">${fmt(modal)}</td>
                </tr>`;
        });

        if (!res.data.length) {
            rows = '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>';
        }

        $('#bodyModal').html(rows);
        $('#cModalHalaman, #footerModal').text(fmt(modalHalaman));
        $('#cJumlahHalaman').text(res.data.length + ' item di halaman ini');
        $('#infoHalaman').text(`Halaman ${res.current_page} dari ${res.last_page} (total ${res.total} barang)`);

        // Pagination
        let pg = '';
        if (res.prev_page_url)
            pg += `<li class="page-item"><a class="page-link" href="#" data-page="${res.current_page - 1}">‹</a></li>`;

        const delta = 2, cur = res.current_page, last = res.last_page;
        for (let p = Math.max(1, cur - delta); p <= Math.min(last, cur + delta); p++) {
            pg += `<li class="page-item ${p === cur ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        }
        if (res.next_page_url)
            pg += `<li class="page-item"><a class="page-link" href="#" data-page="${res.current_page + 1}">›</a></li>`;

        $('#pagination').html(pg);
    });
}

$(document).ready(function () {
    // Load awal
    loadData();

    $('#btnTampilkan').on('click', function () {
        currentSearch = $('#searchBarang').val().trim();
        currentJenis  = $('#filterJenis').val();
        loadData(1);
    });

    $('#btnReset').on('click', function () {
        $('#searchBarang').val('');
        $('#filterJenis').val('');
        currentSearch = '';
        currentJenis  = '';
        loadData(1);
    });

    $('#searchBarang').on('keypress', function (e) {
        if (e.which === 13) $('#btnTampilkan').trigger('click');
    });

    $(document).on('click', '#pagination .page-link', function (e) {
        e.preventDefault();
        loadData($(this).data('page'));
    });
});
</script>
@endpush
