@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Transaksi Motor</h4>
        <h6>Rekap Omzet & Keuntungan</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-success no-print" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print
        </button>
    </div>
</div>

<div class="card no-print">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" id="laporanDari" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" id="laporanSampai" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="btnLaporan">Tampilkan</button>
            </div>
        </div>
    </div>
</div>

<div id="hasilLaporan" style="display:none;">

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card text-center border-secondary h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Transaksi</div>
                    <div class="fs-4 fw-bold" id="sumCount">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-primary h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Omzet</div>
                    <div class="fs-5 fw-bold text-primary" id="sumOmzet">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Modal Barang</div>
                    <div class="fs-5 fw-bold text-warning" id="sumModal">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success h-100">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Keuntungan Bersih</div>
                    <div class="fs-4 fw-bold text-success" id="sumUntung">Rp 0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RINCIAN KEUNTUNGAN -->
    <div class="row g-3 mb-3 no-print">
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body py-2 px-3">
                    <small class="text-muted">Keuntungan Barang</small>
                    <div class="fw-bold text-success" id="sumUntungBarang">Rp 0</div>
                    <small class="text-muted">(Harga Jual - Harga Beli) × Qty</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body py-2 px-3">
                    <small class="text-muted">Keuntungan Jasa</small>
                    <div class="fw-bold text-success" id="sumUntungJasa">Rp 0</div>
                    <small class="text-muted">(100% dari pendapatan jasa)</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <!-- Header print only -->
            <div class="text-center py-3 d-none d-print-block">
                <h5 class="fw-bold mb-0">ARMOR GARAGE - SHOWROOM & SERVICE</h5>
                <div>Laporan Transaksi Motor</div>
                <div class="text-muted small" id="periodeLabel"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="tabelLaporan">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-center">#</th>
                            <th rowspan="2" class="align-middle">Tgl</th>
                            <th rowspan="2" class="align-middle">No. Transaksi</th>
                            <th rowspan="2" class="align-middle">Customer / Motor</th>
                            <th colspan="3" class="text-center bg-light">Barang</th>
                            <th colspan="2" class="text-center bg-light">Jasa</th>
                            <th rowspan="2" class="align-middle text-end">Grand Total</th>
                            <th rowspan="2" class="align-middle text-end text-success">Keuntungan</th>
                        </tr>
                        <tr>
                            <th class="text-end">Omzet</th>
                            <th class="text-end">Modal</th>
                            <th class="text-end">Untung</th>
                            <th class="text-end">Omzet</th>
                            <th class="text-end">Untung</th>
                        </tr>
                    </thead>
                    <tbody id="bodyLaporan">
                    </tbody>
                    <tfoot id="footLaporan">
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
    @media print {
        .no-print { display: none !important; }
        .d-print-block { display: block !important; }
        .page-header .page-btn { display: none; }
    }
    .d-print-block { display: none; }
</style>
<script>
function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

$('#btnLaporan').on('click', function () {
    const dari   = $('#laporanDari').val();
    const sampai = $('#laporanSampai').val();

    $(this).prop('disabled', true).text('Memuat...');

    $.get("{{ route('transaksi-motor.laporan.data') }}", {
        tanggal_dari:   dari,
        tanggal_sampai: sampai,
    }, function (res) {
        const tbody = $('#bodyLaporan');
        tbody.empty();

        if (!res.data.length) {
            tbody.append('<tr><td colspan="11" class="text-center text-muted py-3">Tidak ada data pada periode ini.</td></tr>');
            $('#hasilLaporan').show();
            return;
        }

        res.data.forEach(function (item, i) {
            const motor = [item.plat_nomor, item.nama_motor].filter(Boolean).join(' - ') || '-';
            tbody.append(`
                <tr>
                    <td class="text-center">${i + 1}</td>
                    <td style="white-space:nowrap">${item.tanggal}</td>
                    <td style="white-space:nowrap">${item.nomor_transaksi}</td>
                    <td>
                        <div class="fw-semibold">${item.nama_customer}</div>
                        <small class="text-muted">${motor}</small>
                    </td>
                    <td class="text-end">${fmt(item.omzet_barang)}</td>
                    <td class="text-end text-muted">${fmt(item.modal_barang)}</td>
                    <td class="text-end text-success">${fmt(item.untung_barang)}</td>
                    <td class="text-end">${fmt(item.omzet_jasa)}</td>
                    <td class="text-end text-success">${fmt(item.untung_jasa)}</td>
                    <td class="text-end fw-bold">${fmt(item.total)}</td>
                    <td class="text-end fw-bold text-success">${fmt(item.keuntungan)}</td>
                </tr>
            `);
        });

        $('#footLaporan').html(`
            <tr class="table-secondary fw-bold">
                <td colspan="4" class="text-end">TOTAL</td>
                <td class="text-end">${fmt(res.total_omzet_barang)}</td>
                <td class="text-end text-muted">${fmt(res.total_modal)}</td>
                <td class="text-end text-success">${fmt(res.total_untung_barang)}</td>
                <td class="text-end">${fmt(res.total_omzet_jasa)}</td>
                <td class="text-end text-success">${fmt(res.total_untung_jasa)}</td>
                <td class="text-end">${fmt(res.grand_omzet)}</td>
                <td class="text-end text-success fs-6">${fmt(res.grand_keuntungan)}</td>
            </tr>
        `);

        // Summary cards
        $('#sumCount').text(res.data.length);
        $('#sumOmzet').text(fmt(res.grand_omzet));
        $('#sumModal').text(fmt(res.total_modal));
        $('#sumUntung').text(fmt(res.grand_keuntungan));
        $('#sumUntungBarang').text(fmt(res.total_untung_barang));
        $('#sumUntungJasa').text(fmt(res.total_untung_jasa));

        let periode = '';
        if (dari && sampai) periode = dari + ' s/d ' + sampai;
        else if (dari)      periode = 'Mulai ' + dari;
        else if (sampai)    periode = 'Sampai ' + sampai;
        else                periode = 'Semua Periode';
        $('#periodeLabel').text('Periode: ' + periode);

        $('#hasilLaporan').show();
    }).always(() => {
        $('#btnLaporan').prop('disabled', false).text('Tampilkan');
    });
});
</script>
@endpush
