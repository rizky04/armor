@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Keuangan</h4>
        <h6>Rekap Pemasukan, Pengeluaran & Laba Bersih</h6>
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
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" id="lDari" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" id="lSampai" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="btnLaporan">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>
</div>

<div id="hasilLaporan" style="display:none;">

    <!-- PRINT HEADER -->
    <div class="d-none d-print-block text-center mb-3">
        <h4 class="fw-bold mb-0">ARMOR GARAGE - SHOWROOM & SERVICE</h4>
        <div class="fs-6">Laporan Keuangan</div>
        <div class="text-muted small" id="periodeLabel"></div>
    </div>

    <!-- ===== 4 BAR UTAMA ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">1. Total Omzet Kotor</div>
                            <div class="fs-5 fw-bold text-primary" id="cOmzet">Rp 0</div>
                            <div class="mt-2 small">
                                <div>Showroom (Barang+Jasa): <span class="fw-semibold" id="cOmzetTrx">Rp 0</span></div>
                                <div>Penjualan Barang (Platform): <span class="fw-semibold" id="cOmzetPjl">Rp 0</span></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chart-line fa-2x text-primary opacity-25 mt-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">2. Total Potongan & Modal Barang</div>
                            <div class="fs-5 fw-bold text-warning" id="cModal">Rp 0</div>
                            <div class="mt-2 small">
                                <div>Modal Showroom: <span class="fw-semibold" id="cModalTrx">Rp 0</span></div>
                                <div>Modal Platform: <span class="fw-semibold" id="cModalPjl">Rp 0</span></div>
                                <div>Biaya Platform: <span class="fw-semibold" id="cBiayaPlatform">Rp 0</span></div>
                                <div>Diskon Showroom: <span class="fw-semibold" id="cDiskonShowroom">Rp 0</span></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-boxes-stacked fa-2x text-warning opacity-25 mt-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">3. Total Pengeluaran</div>
                            <div class="fs-5 fw-bold text-danger" id="cPengeluaran">Rp 0</div>
                            <div class="mt-2 small">
                                <div>Operasional: <span class="fw-semibold" id="cPengeluaranOps">Rp 0</span></div>
                                <div>Pembelian Barang: <span class="fw-semibold" id="cPengeluaranPembelian">Rp 0</span></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-trend-down fa-2x text-danger opacity-25 mt-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100" id="cardLaba">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">4. Laba Bersih</div>
                            <div class="fs-4 fw-bold" id="cLaba">Rp 0</div>
                            <div class="mt-2 small text-muted">Omzet Kotor − Potongan&Modal − Pengeluaran</div>
                        </div>
                        <i class="fa-solid fa-sack-dollar fa-2x opacity-25 mt-1" id="iconLaba"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DETAIL 3 KOLOM ===== -->
    <div class="row g-3 mb-4">

        <!-- TRANSAKSI MOTOR -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-motorcycle me-1 text-primary"></i>
                    Transaksi Motor
                    <span class="badge bg-primary ms-1 float-end" id="dTrxJml">0</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr><td class="ps-3">Omzet Barang</td><td class="text-end pe-3" id="dTrxOmzetBarang">Rp 0</td></tr>
                            <tr class="table-light"><td class="ps-3">Omzet Jasa</td><td class="text-end pe-3" id="dTrxOmzetJasa">Rp 0</td></tr>
                            <tr><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Modal Barang</td><td class="text-end pe-3 text-warning" id="dTrxModal">Rp 0</td></tr>
                            <tr class="table-light"><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Untung Barang</td><td class="text-end pe-3 text-success" id="dTrxUntungBarang">Rp 0</td></tr>
                            <tr><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Untung Jasa</td><td class="text-end pe-3 text-success" id="dTrxUntungJasa">Rp 0</td></tr>
                            <tr class="border-top fw-bold">
                                <td class="ps-3">Keuntungan</td>
                                <td class="text-end pe-3 text-success" id="dTrxKeuntungan">Rp 0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PENJUALAN PLATFORM -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-shop me-1 text-warning"></i>
                    Penjualan Barang (Platform)
                    <span class="badge bg-warning text-dark ms-1 float-end" id="dPjlJml">0</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr><td class="ps-3">Total Omzet</td><td class="text-end pe-3" id="dPjlOmzet">Rp 0</td></tr>
                            <tr class="table-light"><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Modal Barang</td><td class="text-end pe-3 text-warning" id="dPjlModal">Rp 0</td></tr>
                            <tr><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Biaya Admin</td><td class="text-end pe-3 text-danger" id="dPjlBiayaAdmin">Rp 0</td></tr>
                            <tr class="table-light"><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Biaya Pengiriman</td><td class="text-end pe-3 text-danger" id="dPjlBiayaKirim">Rp 0</td></tr>
                            <tr><td class="ps-3 text-muted" style="padding-left:1.5rem!important">↳ Biaya Lainnya</td><td class="text-end pe-3 text-danger" id="dPjlBiayaLain">Rp 0</td></tr>
                            <tr class="table-light"><td class="ps-3">Penghasilan Bersih</td><td class="text-end pe-3 text-primary" id="dPjlPenghasilan">Rp 0</td></tr>
                            <tr class="border-top fw-bold">
                                <td class="ps-3">Laba</td>
                                <td class="text-end pe-3 text-success" id="dPjlLaba">Rp 0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PENGELUARAN PER KATEGORI -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-circle-arrow-down me-1 text-danger"></i>
                    Pengeluaran per Kategori
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody id="bodyKategori">
                            <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada pengeluaran.</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold border-top">
                                <td class="ps-3">Total</td>
                                <td class="text-end pe-3 text-danger" id="dTotalPengeluaran">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PEMBELIAN BARANG ===== -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light fw-bold">
                    <i class="fa-solid fa-cart-shopping me-1 text-warning"></i>
                    Pembelian Barang (Stok Masuk)
                    <span class="badge bg-warning text-dark ms-2" id="dPembelianJml">0 item</span>
                    <span class="float-end fw-bold text-danger" id="dTotalPembelian">Rp 0</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Kulak</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetailPembelian">
                                <tr><td colspan="7" class="text-center text-muted">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RUMUS LABA BERSIH ===== -->
    <div class="card mb-4" id="cardLabaBesar">
        <div class="card-body py-4 text-center">
            <div class="text-muted mb-2 small" id="rumusLabel"></div>
            <div class="display-5 fw-bold" id="labaBersihBesar">Rp 0</div>
            <div class="text-muted small mt-2" id="rumusDetail"></div>
            <div class="text-muted small mt-1" id="rumusDetailPembelian"></div>
        </div>
    </div>

    <!-- ===== DETAIL PENGELUARAN ===== -->
    <div class="card no-print">
        <div class="card-header fw-bold">
            <i class="fa-solid fa-list me-1"></i> Detail Pengeluaran Periode Ini
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="bodyDetailPengeluaran">
                        <tr><td colspan="5" class="text-center text-muted">-</td></tr>
                    </tbody>
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
        .page-header .page-btn { display: none !important; }
        .card { break-inside: avoid; }
    }
    .d-print-block { display: none; }
</style>
<script>
function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function setLaba(nilai) {
    const positif = nilai >= 0;
    const cls = positif ? 'text-success' : 'text-danger';
    const borderCls = positif ? 'border-success' : 'border-danger';

    $('#cLaba, #labaBersihBesar').text(fmt(nilai)).removeClass('text-success text-danger').addClass(cls);
    $('#iconLaba').removeClass('text-success text-danger').addClass(cls);
    $('#cardLaba').removeClass('border-success border-danger').addClass(borderCls);
    $('#cardLabaBesar').removeClass('border-success border-danger').addClass(borderCls);
}

$('#btnLaporan').on('click', function () {
    const dari   = $('#lDari').val();
    const sampai = $('#lSampai').val();

    $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Memuat...');

    $.get("{{ route('akuntansi.laporan.data') }}", {
        tanggal_dari:   dari,
        tanggal_sampai: sampai,
    }, function (res) {
        const trx = res.transaksi_motor;
        const pjl = res.penjualan_platform;
        const pg  = res.pengeluaran;
        const pb  = res.pembelian_barang;
        const ok  = res.omzet_kotor;
        const pm  = res.potongan_modal;
        const ps  = res.pengeluaran_semua;
        const lb  = res.laba_bersih_baru;

        // ===== BAR 1: OMZET KOTOR =====
        $('#cOmzet').text(fmt(ok.total));
        $('#cOmzetTrx').text(fmt(ok.showroom));
        $('#cOmzetPjl').text(fmt(ok.platform));

        // ===== BAR 2: POTONGAN & MODAL BARANG =====
        $('#cModal').text(fmt(pm.total));
        $('#cModalTrx').text(fmt(pm.modal_showroom));
        $('#cModalPjl').text(fmt(pm.modal_platform));
        $('#cBiayaPlatform').text(fmt(pm.biaya_platform));
        $('#cDiskonShowroom').text(fmt(pm.diskon_showroom));

        // ===== BAR 3: PENGELUARAN =====
        $('#cPengeluaran').text(fmt(ps.total));
        $('#cPengeluaranOps').text(fmt(ps.operasional));
        $('#cPengeluaranPembelian').text(fmt(ps.pembelian_barang));

        // ===== BAR 4: LABA BERSIH =====
        setLaba(lb);

        // ===== TRANSAKSI MOTOR =====
        $('#dTrxJml').text(trx.jumlah);
        $('#dTrxOmzetBarang').text(fmt(trx.omzet_barang));
        $('#dTrxOmzetJasa').text(fmt(trx.omzet_jasa));
        $('#dTrxModal').text(fmt(trx.modal));
        $('#dTrxUntungBarang').text(fmt(trx.untung_barang));
        $('#dTrxUntungJasa').text(fmt(trx.untung_jasa));
        $('#dTrxKeuntungan').text(fmt(trx.keuntungan));

        // ===== PENJUALAN PLATFORM =====
        $('#dPjlJml').text(pjl.jumlah);
        $('#dPjlOmzet').text(fmt(pjl.omzet));
        $('#dPjlModal').text(fmt(pjl.modal));
        $('#dPjlBiayaAdmin').text(fmt(pjl.biaya_admin));
        $('#dPjlBiayaKirim').text(fmt(pjl.biaya_kirim));
        $('#dPjlBiayaLain').text(fmt(pjl.biaya_lain));
        $('#dPjlPenghasilan').text(fmt(pjl.penghasilan));
        $('#dPjlLaba').text(fmt(pjl.laba));

        // ===== PENGELUARAN PER KATEGORI =====
        const bodyKat = $('#bodyKategori');
        bodyKat.empty();
        const perKat = pg.per_kategori;
        if (!Object.keys(perKat).length) {
            bodyKat.append('<tr><td colspan="2" class="text-center text-muted py-3">Tidak ada pengeluaran.</td></tr>');
        } else {
            Object.entries(perKat).forEach(([kat, jml]) => {
                bodyKat.append(`
                    <tr>
                        <td class="ps-3">${kat}</td>
                        <td class="text-end pe-3 text-danger">${fmt(jml)}</td>
                    </tr>
                `);
            });
        }
        $('#dTotalPengeluaran').text(fmt(pg.total));

        // ===== LABA BERSIH BESAR =====
        $('#rumusLabel').text('Total Omzet Kotor − Total Potongan & Modal Barang − Total Pengeluaran = Laba Bersih');
        $('#rumusDetail').html(
            `${fmt(ok.total)} &minus; ${fmt(pm.total)} &minus; ${fmt(ps.total)} = <strong>${fmt(lb)}</strong>`
        );

        // ===== DETAIL PEMBELIAN BARANG =====
        const bodyPb = $('#bodyDetailPembelian');
        bodyPb.empty();
        $('#dPembelianJml').text(pb.detail.length + ' item');
        $('#dTotalPembelian').text(fmt(pb.total));
        if (!pb.detail.length) {
            bodyPb.append('<tr><td colspan="7" class="text-center text-muted">Tidak ada pembelian barang.</td></tr>');
        } else {
            pb.detail.forEach((item, i) => {
                bodyPb.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.tgl_pembelian}</td>
                        <td>${item.kode_barang}</td>
                        <td>${item.nama_barang}</td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="text-end">${fmt(item.harga_kulak)}</td>
                        <td class="text-end text-danger fw-semibold">${fmt(item.total)}</td>
                    </tr>
                `);
            });
        }

        // ===== DETAIL PENGELUARAN =====
        const bodyDet = $('#bodyDetailPengeluaran');
        bodyDet.empty();
        if (!pg.detail.length) {
            bodyDet.append('<tr><td colspan="5" class="text-center text-muted">Tidak ada pengeluaran.</td></tr>');
        } else {
            pg.detail.forEach((item, i) => {
                bodyDet.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.tanggal}</td>
                        <td><span class="badge bg-secondary">${item.kategori}</span></td>
                        <td>${item.keterangan}</td>
                        <td class="text-end text-danger fw-semibold">${fmt(item.jumlah)}</td>
                    </tr>
                `);
            });
        }

        // ===== PERIODE =====
        let periode = '';
        if (dari && sampai) periode = dari + ' s/d ' + sampai;
        else if (dari)      periode = 'Mulai ' + dari;
        else if (sampai)    periode = 'Sampai ' + sampai;
        else                periode = 'Semua Periode';
        $('#periodeLabel').text('Periode: ' + periode);

        $('#hasilLaporan').show();

    }).always(() => {
        $('#btnLaporan').prop('disabled', false).html('<i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan');
    });
});
</script>
@endpush
