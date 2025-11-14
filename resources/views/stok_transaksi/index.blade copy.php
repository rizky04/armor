@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Transaksi Stok Barang</h4>
        <h6>Kelola barang keluar, rusak, return pembelian & penjualan</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Cari barang...">
            </div>
            <div class="col-md-3">
                <select id="jenis_transaksi" class="form-control">
                    <option value="">Semua Transaksi</option>
                    <option value="masuk">Barang Masuk</option>
                    <option value="keluar">Barang Keluar</option>
                    <option value="rusak">Barang Rusak</option>
                    <option value="return_pembelian">Return Pembelian</option>
                    <option value="return_penjualan">Return Penjualan</option>
                    <option value="koreksi">Koreksi</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" id="btnAdd">+ Tambah Transaksi</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="transaksi-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis Transaksi</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
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

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="transaksiModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="transaksiForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Transaksi Stok</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Pilih Barang</label>
                    <select id="barang_select" name="id_barang" class="form-control" required></select>
                </div>
                <div class="col-md-6 mb-2">
                    <label>Jenis Transaksi</label>
                    <select id="jenis_transaksi_input" name="jenis_transaksi" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                        <option value="rusak">Barang Rusak</option>
                        <option value="return_pembelian">Return Pembelian</option>
                        <option value="return_penjualan">Return Penjualan</option>
                        <option value="koreksi">Koreksi</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label>Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" required>
                </div>
                <div class="col-md-8 mb-2">
                    <label>Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control">
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let searchQuery = '';
let jenisFilter = '';

function loadTransaksi(page = 1, search = '', jenis = '') {
    $.get("{{ route('stok-transaksi.data') }}", { page, search, jenis }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(t => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${t.barang?.kode_barang ?? '-'}</td>
                    <td>${t.barang?.nama_barang ?? '-'}</td>
                    <td><span class="badge bg-info">${t.jenis_transaksi}</span></td>
                    <td>${t.jumlah}</td>
                    <td>${t.keterangan ?? '-'}</td>
                    <td>${t.created_at ? new Date(t.created_at).toLocaleString() : '-'}</td>
                    <td>${t.created_by ?? '-'}</td>
                </tr>`;
        });
        $('#transaksi-table tbody').html(rows);

        // pagination
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadTransaksi(${current - 1}, searchQuery, jenisFilter)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadTransaksi(${i}, searchQuery, jenisFilter)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadTransaksi(${current + 1}, searchQuery, jenisFilter)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadTransaksi();

    // search & filter
    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadTransaksi(1, searchQuery, jenisFilter);
    });
    $('#jenis_transaksi').on('change', function() {
        jenisFilter = $(this).val();
        loadTransaksi(1, searchQuery, jenisFilter);
    });

    // modal open
    $('#btnAdd').on('click', function() {
        $('#transaksiForm')[0].reset();
        $('#barang_select').val(null).trigger('change');
        $('#transaksiModal').modal('show');
    });

    // select2 barang
    $('#barang_select').select2({
        dropdownParent: $('#transaksiModal'),
        placeholder: 'Pilih Barang...',
        ajax: {
            url: "{{ route('select2.barang') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(b => ({ id: b.id_barang, text: `${b.kode_barang} - ${b.nama_barang}` }))
            })
        }
    });

    // submit transaksi
    $('#transaksiForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('stok-transaksi.store') }}",
            method: "POST",
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                $('#transaksiModal').modal('hide');
                Swal.fire('Berhasil!', res.message, 'success');
                loadTransaksi(currentPage, searchQuery, jenisFilter);
            },
            error: function(xhr) {
                Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });
});
</script>
@endpush
