@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Penjualan Barang</h4>
        <h6>Omzet, Modal & Laba per Platform</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-success no-print" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>
    </div>
</div>

<div class="card no-print mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Dari</label><input type="date" id="lDari" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Sampai</label><input type="date" id="lSampai" class="form-control"></div>
            <div class="col-md-3">
                <label class="form-label">Platform</label>
                <select id="lPlatform" class="form-select">
                    <option value="">Semua Platform</option>
                    @foreach($platformList as $p)<option value="{{ $p }}">{{ $p }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100" id="btnLaporan">Tampilkan</button></div>
        </div>
    </div>
</div>

<div id="hasilLaporan" style="display:none;">

    <div class="d-none d-print-block text-center mb-3">
        <h4 class="fw-bold mb-0">ARMOR GARAGE</h4>
        <div>Laporan Penjualan Barang Platform</div>
        <div class="small text-muted" id="periodeLabel"></div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="text-muted small">Transaksi</div>
                    <div class="fs-4 fw-bold" id="sCount">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Omzet</div>
                    <div class="fs-6 fw-bold text-primary" id="sOmzet">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Modal</div>
                    <div class="fs-6 fw-bold text-warning" id="sModal">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Biaya Platform</div>
                    <div class="fs-6 fw-bold text-danger" id="sBiayaAdmin">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="text-muted small">Penghasilan Bersih</div>
                    <div class="fs-6 fw-bold text-info" id="sPenghasilan">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm" id="cardLaba">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Laba Bersih</div>
                    <div class="fs-5 fw-bold" id="sLaba">Rp 0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tgl</th>
                            <th>No. Penjualan</th>
                            <th>Platform</th>
                            <th>No. Pesanan</th>
                            <th>Pembeli</th>
                            <th class="text-end">Omzet</th>
                            <th class="text-end">Modal</th>
                            <th class="text-end">Biaya</th>
                            <th class="text-end">Penghasilan</th>
                            <th class="text-end text-success">Laba</th>
                        </tr>
                    </thead>
                    <tbody id="bodyLaporan"></tbody>
                    <tfoot id="footLaporan"></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @media print { .no-print { display:none!important; } .d-print-block { display:block!important; } .page-header .page-btn { display:none!important; } }
    .d-print-block { display:none; }
</style>
<script>
function fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

$('#btnLaporan').on('click', function() {
    $(this).prop('disabled', true).text('Memuat...');
    $.get("{{ route('penjualan-platform.laporan.data') }}", {
        tanggal_dari:   $('#lDari').val(),
        tanggal_sampai: $('#lSampai').val(),
        platform:       $('#lPlatform').val(),
    }, function(res) {
        const tbody = $('#bodyLaporan');
        tbody.empty();

        if (!res.data.length) {
            tbody.append('<tr><td colspan="11" class="text-center text-muted py-3">Tidak ada data.</td></tr>');
            $('#hasilLaporan').show();
            return;
        }

        res.data.forEach(function(item, i) {
            const totalBiaya = item.biaya_admin + item.biaya_pengiriman + item.biaya_lainnya;
            const labaClass  = item.laba_bersih >= 0 ? 'text-success' : 'text-danger';
            tbody.append(`
                <tr>
                    <td>${i+1}</td>
                    <td>${item.tanggal}</td>
                    <td><small>${item.nomor_penjualan}</small></td>
                    <td><span class="badge bg-warning text-dark">${item.platform}</span></td>
                    <td><small class="text-muted">${item.nomor_pesanan ?? '-'}</small></td>
                    <td>${item.nama_pembeli ?? '-'}</td>
                    <td class="text-end">${fmt(item.total_harga_jual)}</td>
                    <td class="text-end text-warning">${fmt(item.total_modal)}</td>
                    <td class="text-end text-danger">${fmt(totalBiaya)}</td>
                    <td class="text-end">${fmt(item.penghasilan_bersih)}</td>
                    <td class="text-end fw-bold ${labaClass}">${fmt(item.laba_bersih)}</td>
                </tr>
            `);
        });

        $('#footLaporan').html(`
            <tr class="table-secondary fw-bold">
                <td colspan="6" class="text-end">TOTAL (${res.data.length} transaksi)</td>
                <td class="text-end">${fmt(res.total_omzet)}</td>
                <td class="text-end text-warning">${fmt(res.total_modal)}</td>
                <td class="text-end text-danger">${fmt(res.total_biaya_admin + res.total_biaya_lainnya)}</td>
                <td class="text-end">${fmt(res.total_penghasilan)}</td>
                <td class="text-end ${res.total_laba >= 0 ? 'text-success' : 'text-danger'}">${fmt(res.total_laba)}</td>
            </tr>
        `);

        // Cards
        $('#sCount').text(res.data.length);
        $('#sOmzet').text(fmt(res.total_omzet));
        $('#sModal').text(fmt(res.total_modal));
        $('#sBiayaAdmin').text(fmt(res.total_biaya_admin + res.total_biaya_lainnya));
        $('#sPenghasilan').text(fmt(res.total_penghasilan));
        $('#sLaba').text(fmt(res.total_laba)).removeClass('text-success text-danger').addClass(res.total_laba >= 0 ? 'text-success' : 'text-danger');

        // Periode
        const dari   = $('#lDari').val(), sampai = $('#lSampai').val();
        let periode = dari && sampai ? `${dari} s/d ${sampai}` : (dari ? 'Mulai '+dari : (sampai ? 'Sampai '+sampai : 'Semua Periode'));
        $('#periodeLabel').text('Periode: ' + periode);

        $('#hasilLaporan').show();
    }).always(() => $('#btnLaporan').prop('disabled', false).text('Tampilkan'));
});
</script>
@endpush
