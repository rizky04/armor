@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Penjualan Barang</h4>
        <h6>Penjualan via Platform (Shopee, dll)</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('penjualan-platform.create') }}" class="btn btn-added">
            <i class="fa-solid fa-plus me-1"></i> Tambah Penjualan
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Dari</label>
                <input type="date" id="fDari" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai</label>
                <input type="date" id="fSampai" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Platform</label>
                <select id="fPlatform" class="form-select">
                    <option value="">Semua</option>
                    @foreach($platformList as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="fStatus" class="form-select">
                    <option value="">Semua</option>
                    <option value="selesai">Selesai</option>
                    <option value="pending">Pending</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" id="fSearch" class="form-control" placeholder="No. pesanan / nama pembeli...">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100" id="btnFilter">Cari</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>No. Penjualan</th>
                        <th>Platform</th>
                        <th>No. Pesanan</th>
                        <th>Pembeli</th>
                        <th>Nama Barang</th>
                        <th>Tgl</th>
                        <th class="text-end">Total Jual</th>
                        <th class="text-end">Biaya</th>
                        <th class="text-end">Penghasilan</th>
                        <th class="text-end text-success">Laba</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bodyPenjualan">
                    <tr><td colspan="12" class="text-center text-muted">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

const PLATFORM_BADGE = {
    'Shopee': 'bg-warning text-dark',
    'Tokopedia': 'bg-success',
    'Lazada': 'bg-primary',
    'TikTok Shop': 'bg-dark',
    'Instagram': 'bg-danger',
    'Offline': 'bg-secondary',
    'Lainnya': 'bg-light text-dark border',
};

function loadData() {
    $.get("{{ route('penjualan-platform.data') }}", {
        tanggal_dari:   $('#fDari').val(),
        tanggal_sampai: $('#fSampai').val(),
        platform:       $('#fPlatform').val(),
        status:         $('#fStatus').val(),
        search:         $('#fSearch').val(),
    }, function(res) {
        const tbody = $('#bodyPenjualan');
        tbody.empty();

        if (!res.data.length) {
            tbody.append('<tr><td colspan="13" class="text-center text-muted">Tidak ada data.</td></tr>');
            return;
        }

        res.data.forEach(function(item, i) {
            const badgePlatform = `<span class="badge ${PLATFORM_BADGE[item.platform] ?? 'bg-secondary'}">${item.platform}</span>`;
            const badgeStatus = {
                selesai: '<span class="badge bg-success">Selesai</span>',
                pending: '<span class="badge bg-warning text-dark">Pending</span>',
                dibatalkan: '<span class="badge bg-danger">Dibatalkan</span>',
            }[item.status] ?? '';

            const totalBiaya = (item.total_harga_jual - item.penghasilan_bersih);
            const labaClass  = item.laba_bersih >= 0 ? 'text-success' : 'text-danger';

            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td><small>${item.nomor_penjualan}</small></td>
                    <td>${badgePlatform}</td>
                    <td><small class="text-muted">${item.nomor_pesanan}</small></td>
                    <td>${item.nama_pembeli}</td>
                    <td><small>${item.nama_barang_list.join(', ')}</small></td>
                    <td>${item.tanggal}</td>
                    <td class="text-end">${fmt(item.total_harga_jual)}</td>
                    <td class="text-end text-danger"><small>${fmt(totalBiaya)}</small></td>
                    <td class="text-end">${fmt(item.penghasilan_bersih)}</td>
                    <td class="text-end fw-bold ${labaClass}">${fmt(item.laba_bersih)}</td>
                    <td>${badgeStatus}</td>
                    <td>
                        <a href="/penjualan-platform/${item.id}" class="btn btn-sm btn-info"><i class="fa-solid fa-eye"></i></a>
                        ${item.status !== 'selesai' ? `<a href="/penjualan-platform/${item.id}/edit" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>` : ''}
                        <a href="/penjualan-platform/${item.id}/print" target="_blank" class="btn btn-sm btn-secondary"><i class="fa-solid fa-print"></i></a>
                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${item.id}"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `);
        });
    });
}

$(document).ready(function() {
    loadData();
    $('#btnFilter').on('click', loadData);
    $('#fSearch').on('keypress', e => { if (e.which === 13) loadData(); });

    $(document).on('click', '.btn-hapus', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus penjualan ini?',
            text: 'Stok barang akan dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: `/penjualan-platform/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: res => { Swal.fire('Dihapus!', res.message, 'success'); loadData(); },
                error: err => Swal.fire('Gagal!', err.responseJSON?.message, 'error'),
            });
        });
    });
});
</script>
@endpush
