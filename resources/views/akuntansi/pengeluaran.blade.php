@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Pengeluaran</h4>
        <h6>Biaya Operasional & Tak Terduga</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fa-solid fa-plus me-1"></i> Tambah Pengeluaran
        </button>
    </div>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" id="fDari" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai</label>
                <input type="date" id="fSampai" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select id="fKategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" id="fSearch" class="form-control" placeholder="Keterangan...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="btnFilter">Cari</button>
            </div>
        </div>
    </div>
</div>

<!-- TOTAL CARD -->
<div class="row g-3 mb-3">
    <div class="col-md-12">
        <div class="card border-danger">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="text-muted small">Total Pengeluaran (periode dipilih)</div>
                    <div class="fs-4 fw-bold text-danger" id="totalPengeluaran">Rp 0</div>
                </div>
                <i class="fa-solid fa-arrow-trend-down fa-2x text-danger opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah</th>
                        <th>Catatan</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bodyPengeluaran">
                    <tr><td colspan="8" class="text-center text-muted">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" id="inTanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select id="inKategori" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                    <input type="text" id="inKeterangan" class="form-control" placeholder="Contoh: Bayar PLN bulan Mei">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <input type="number" id="inJumlah" class="form-control" placeholder="0" min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea id="inCatatan" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

const BADGE_COLOR = {
    'Gaji Karyawan': 'bg-danger',
    'Listrik': 'bg-warning text-dark',
    'Air': 'bg-info',
    'Sewa Tempat': 'bg-primary',
    'Peralatan': 'bg-secondary',
    'Transportasi': 'bg-success',
    'Operasional': 'bg-dark',
    'Lain-lain': 'bg-light text-dark border',
};

function loadData() {
    $.get("{{ route('pengeluaran.data') }}", {
        tanggal_dari:   $('#fDari').val(),
        tanggal_sampai: $('#fSampai').val(),
        kategori:       $('#fKategori').val(),
        search:         $('#fSearch').val(),
    }, function (res) {
        const tbody = $('#bodyPengeluaran');
        tbody.empty();

        $('#totalPengeluaran').text(fmt(res.total));

        if (!res.data.length) {
            tbody.append('<tr><td colspan="8" class="text-center text-muted">Tidak ada data.</td></tr>');
            return;
        }

        res.data.forEach(function (item, i) {
            const badge = `<span class="badge ${BADGE_COLOR[item.kategori] ?? 'bg-secondary'}">${item.kategori}</span>`;
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.tanggal}</td>
                    <td>${badge}</td>
                    <td>${item.keterangan}</td>
                    <td class="text-end fw-semibold text-danger">${fmt(item.jumlah)}</td>
                    <td><small class="text-muted">${item.catatan ?? '-'}</small></td>
                    <td><small>${item.creator?.name ?? '-'}</small></td>
                    <td>
                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${item.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    });
}

$(document).ready(function () {
    loadData();

    $('#btnFilter').on('click', loadData);
    $('#fSearch').on('keypress', e => { if (e.which === 13) loadData(); });

    $('#btnSimpan').on('click', function () {
        const payload = {
            tanggal:    $('#inTanggal').val(),
            kategori:   $('#inKategori').val(),
            keterangan: $('#inKeterangan').val().trim(),
            jumlah:     $('#inJumlah').val(),
            catatan:    $('#inCatatan').val().trim(),
            _token:     '{{ csrf_token() }}',
        };

        if (!payload.tanggal || !payload.kategori || !payload.keterangan || !payload.jumlah) {
            return Swal.fire('Perhatian!', 'Tanggal, kategori, keterangan, dan jumlah wajib diisi.', 'warning');
        }

        $.post("{{ route('pengeluaran.store') }}", payload, function (res) {
            Swal.fire('Berhasil!', res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalTambah')).hide();
            // reset form
            $('#inKategori').val('');
            $('#inKeterangan').val('');
            $('#inJumlah').val('');
            $('#inCatatan').val('');
            loadData();
        }).fail(err => {
            Swal.fire('Gagal!', err.responseJSON?.message ?? 'Terjadi kesalahan.', 'error');
        });
    });

    $(document).on('click', '.btn-hapus', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus pengeluaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: `/pengeluaran/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: res => {
                    Swal.fire('Dihapus!', res.message, 'success');
                    loadData();
                },
            });
        });
    });
});
</script>
@endpush
