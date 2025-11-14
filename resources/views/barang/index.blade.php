@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Barang</h4>
        <h6>Manajemen Data Barang</h6>
    </div>
   <div class="page-btn d-flex justify-content-end align-items-center gap-2">
    @can('master-data-barang-create')
    <button class="btn btn-added" id="btn-add">+ Tambah Barang</button>
    @endcan
    <button class="btn btn-success" id="btn-print-all">🖨️ Print QR</button>
</div>

</div>

<div class="card">
    <div class="card-body">

        {{-- Header filter & pencarian --}}
        <div class="row mb-3 align-items-center">
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" id="search" class="form-control" placeholder="Cari barang...">
            </div>
            <div class="col-md-8 text-end">
    {{-- <div class="btn-filter-group d-inline-flex" role="group" aria-label="Filter stok">
        <button class="btn-filter active" id="filter-semua" data-filter="">Semua</button>
        <button class="btn-filter" id="filter-aman" data-filter="aman">Stok <span class="badge bg-success"><i class="fa-solid fa-check"></i></span></button>
        <button class="btn-filter" id="filter-tidak-aman" data-filter="tidak_aman">Stok <span class="badge bg-danger"><i class="fa-solid fa-xmark"></i></span></button>
    </div> --}}
    <div class="btn-filter-group d-inline-flex" role="group" aria-label="Filter stok">
    <button class="btn-filter active" id="filter-semua" data-filter="">Semua</button>
    <button class="btn-filter" id="filter-aman" data-filter="aman">Stok
        <span class="badge bg-success"><i class="fa-solid fa-check"></i></span>
    </button>
    <button class="btn-filter" id="filter-tidak-aman" data-filter="tidak_aman">Stok
        <span class="badge bg-danger"><i class="fa-solid fa-xmark"></i></span>
    </button>

    <!-- Tambahan baru -->
    <button class="btn-filter" id="filter-stok-minim" data-filter="stok_minim">Stok Minim</button>
    <button class="btn-filter" id="filter-terlaris" data-filter="terlaris">Barang Terlaris</button>
    <button class="btn-filter" id="filter-tidak-terlaris" data-filter="tidak_terlaris">Tidak Terlaris</button>
</div>

</div>
        </div>

        {{-- Info jumlah data --}}
        <div class="alert alert-info py-2 px-3 mb-3" id="info-jumlah" style="display:none;">
            Menampilkan <strong id="jumlah-barang">0</strong> barang.
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="barang-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Aksi</th>
                        <th>Kode Sistem</th>
                        <th>Kode Part</th>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Keterangan</th>
                        <th>Lokasi</th>
                        <th>Stok</th>
                        <th>Pagu</th>
                        @can('master-data-barang-harga-jual')
                        <th>Harga Kulak</th>
                        @endcan
                        <th>Harga Jual</th>
                         <th>Total Terjual</th> <!-- BARU -->
    <th>Total Penjualan</th> <!-- BARU -->
                        <th>Distributor</th>
                        <th>Jenis Kendaraan</th>
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

@include('barang.modal.form')
@endsection

@push('scripts')
<style>
.table-danger td { font-weight: 500; }
    .btn-filter-group {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        background: #fff;
    }

    .btn-filter {
        border: none;
        padding: 8px 18px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #fff;
        color: #555;
    }

    .btn-filter:hover {
        background-color: #f5f5f5;
    }

    .btn-filter.active {
        background-color: #007bff;
        color: #fff;
        font-weight: 500;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
    }

    .btn-filter[data-filter="aman"].active {
        background-color: #28a745;
    }

    .btn-filter[data-filter="tidak_aman"].active {
        background-color: #dc3545;
    }
</style>
<script>
let currentPage = 1;
let searchQuery = '';
let filterStok = ''; // aman / tidak_aman / ''

 window.userPermissions = {
                editBarang: @json(auth()->user()->can('master-data-barang-edit')),
                deleteBarang: @json(auth()->user()->can('master-data-barang-delete')),
                hargaKulak: @json(auth()->user()->can('master-data-barang-harga-jual'))
            };

function formatRupiah(angka) {
    if (!angka) return "Rp 0";
    return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function loadBarang(page = 1, search = '', filter = '') {
    $.get("{{ route('barang.data') }}", { page: page, search: search, filter: filter }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;

        res.data.forEach(b => {
            const aman = b.stok_barang >= b.pagu;
            const rowClass = aman ? '' : 'table-danger';
            const badgeStok = aman
                ? `<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> <strong>${b.stok_barang}</strong>`
                : `<span class="badge bg-danger"><i class="fa-solid fa-xmark"></i></span> <strong>${b.stok_barang}</strong>`;

            let btnEdit = '', btnDelete = '', hargaKulakbarang = '';

            if (window.userPermissions.editBarang)
                btnEdit = `<button class="dropdown-item btn-edit" data-id="${b.id_barang}"><i class="fa-solid fa-pencil me-2"></i>Edit</button>`;
            if (window.userPermissions.deleteBarang)
                btnDelete = `<button class="dropdown-item btn-delete" data-id="${b.id_barang}"><i class="fa-solid fa-trash me-2"></i>Delete</button>`;
            if (window.userPermissions.hargaKulak) {
                hargaKulakbarang = `<td>${formatRupiah(b.harga_kulak ?? 0)}</td>`
            }
            rows += `
                <tr class="${rowClass}">
                    <td>${++i}</td>
                    <td class="text-center">
                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item btn-qr" data-id="${b.id_barang}"><i class="fa-solid fa-qrcode me-2"></i>Print</a></li>
                            <li>${btnEdit}</li>
                            <li>${btnDelete}</li>
                        </ul>
                    </td>
                    <td>${b.id_barang}</td>
                    <td>${b.kode_barang}</td>
                    <td>${b.nama_barang}</td>
                    <td>${b.merk_barang ?? ''}</td>
                    <td>${b.keterangan ?? ''}</td>
                    <td>${b.lokasi ?? ''}</td>
                    <td>${badgeStok}</td>
                    <td>${b.pagu ?? 0}</td>
                    ${hargaKulakbarang}
                    <td>${formatRupiah(b.harga_jual ?? 0)}</td>
                       <td>${b.total_terjual ?? 0}</td> <!-- Tampilkan total terjual -->
      <td>${formatRupiah(b.total_penjualan ?? 0)}</td> <!-- Tampilkan total penjualan -->
                    <td>${b.distributor ?? ''}</td>
                    <td>${b.jenis ?? ''}</td>

                </tr>`;
        });

        $('#barang-table tbody').html(rows);
        $('#jumlah-barang').text(res.total);
        $('#info-jumlah').show();

        // pagination tetap sama
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url)
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current - 1}, searchQuery, filterStok)">Prev</a></li>`;
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadBarang(${i}, searchQuery, filterStok)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url)
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current + 1}, searchQuery, filterStok)">Next</a></li>`;
        $('#pagination').html(pagination);
    });
}


$(document).ready(function() {
    loadBarang();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadBarang(1, searchQuery);
    });

       $(document).on('click', '[id^=filter-]', function() {
        $('[id^=filter-]').removeClass('active');
        $(this).addClass('active');
        filterStok = $(this).data('filter');
        loadBarang(1, searchQuery, filterStok);
    });

    $('#btn-add').on('click', function() {
        $('#barangForm')[0].reset();
        $('#id_barang').val('');
        $('#barangModal .modal-title').text('Tambah Barang');
        $('#barangModal').modal('show');
    });

    $('#barangForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#id_barang').val();
        let url = id ? `/barang/${id}` : "{{ route('barang.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                kode_barang: $('#kode_barang').val(),
                nama_barang: $('#nama_barang').val(),
                merk_barang: $('#merk_barang').val(),
                lokasi: $('#lokasi').val(),
                stok_barang: $('#stok_barang').val(),
                pagu: $('#pagu').val(),
                harga_kulak: $('#harga_kulak').val(),
                harga_jual: $('#harga_jual').val(),
                distributor: $('#distributor').val(),
                jenis: $('#jenis').val(),
                hapus: $('#hapus').val(),
                keterangan: $('#keterangan').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#barangModal').modal('hide');
                loadBarang(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/barang/${id}`, function(b) {
            $('#id_barang').val(b.id_barang);
            $('#kode_barang').val(b.kode_barang);
            $('#nama_barang').val(b.nama_barang);
            $('#merk_barang').val(b.merk_barang);
            $('#lokasi').val(b.lokasi);
            $('#stok_barang').val(b.stok_barang);
            $('#pagu').val(b.pagu);
            $('#harga_kulak').val(b.harga_kulak);
            $('#harga_jual').val(b.harga_jual);
            $('#distributor').val(b.distributor);
            $('#jenis').val(b.jenis);
            $('#hapus').val(b.hapus);
            $('#keterangan').val(b.keterangan);
            $('#barangModal .modal-title').text('Edit Barang');
            $('#barangModal').modal('show');
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/barang/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        loadBarang(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});


$(document).on('click', '.btn-qr', function() {
    let id = $(this).data('id');
    window.open(`/generateQr/${id}`, '_blank');
});




// Tombol print semua (bisa kamu taruh di header)
// $('.page-btn').append(`
//     <button class="btn btn-success" id="btn-print-all">🖨️ Print Semua QR</button>
// `);

$(document).on('click', '#btn-print-all', function() {
  window.open(`{{ route('printQr') }}`, '_blank');
});

</script>
@endpush
