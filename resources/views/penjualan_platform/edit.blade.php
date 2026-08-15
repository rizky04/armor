@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Edit Penjualan</h4>
        <h6>{{ $penjualan->nomor_penjualan }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('penjualan-platform.show', $penjualan->id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <!-- INFO PESANAN -->
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-store me-1"></i> Info Pesanan</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label">Platform <span class="text-danger">*</span></label>
                <select id="platform" class="form-select">
                    @foreach($platformList as $p)
                        <option value="{{ $p }}" {{ $p === $penjualan->platform ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">No. Pesanan Platform</label>
                <input type="text" id="nomor_pesanan" class="form-control" value="{{ $penjualan->nomor_pesanan }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama Pembeli</label>
                <input type="text" id="nama_pembeli" class="form-control" value="{{ $penjualan->nama_pembeli }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="tanggal" class="form-control" value="{{ $penjualan->tanggal }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="status" class="form-select">
                    <option value="selesai" {{ $penjualan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ $penjualan->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="dibatalkan" {{ $penjualan->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- BARANG -->
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-box me-1"></i> Barang yang Dijual</h6>
        <div class="row mb-2">
            <div class="col-md-12">
                <select id="barangSelect" style="width:100%" placeholder="Cari & pilih barang dari master...">
                    <option></option>
                </select>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered" id="tabelBarang">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:30%">Nama Barang</th>
                        <th>Harga Kulak<br><small class="text-muted fw-normal">(dari master)</small></th>
                        <th>Harga Jual<br><small class="text-danger fw-normal">*input manual</small></th>
                        <th>Stok</th>
                        <th style="width:8%">Qty</th>
                        <th>Modal</th>
                        <th>Subtotal Jual</th>
                        <th>Laba Item</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="emptyRow"><td colspan="9" class="text-center text-muted">Belum ada barang.</td></tr>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="5" class="text-end">Total</td>
                        <td class="text-end text-warning" id="footModal">Rp 0</td>
                        <td class="text-end text-primary" id="footJual">Rp 0</td>
                        <td class="text-end text-success" id="footLabaItem">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- BIAYA PLATFORM -->
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-receipt me-1"></i> Rincian Biaya Platform</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Biaya Admin / Komisi Platform (Rp)</label>
                <input type="number" id="biaya_admin" class="form-control" value="{{ $penjualan->biaya_admin }}" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Biaya Pengiriman (Rp)</label>
                <input type="number" id="biaya_pengiriman" class="form-control" value="{{ $penjualan->biaya_pengiriman }}" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Biaya Lainnya (Rp)</label>
                <input type="number" id="biaya_lainnya" class="form-control" value="{{ $penjualan->biaya_lainnya }}" min="0">
            </div>
        </div>

        <!-- RINGKASAN -->
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-calculator me-1"></i> Ringkasan Keuangan</h6>
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Total Harga Jual</td>
                            <td class="text-end fw-semibold" id="rTotalJual">Rp 0</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">↳ Biaya Admin</td>
                            <td class="text-end text-danger" id="rBiayaAdmin">− Rp 0</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">↳ Biaya Pengiriman</td>
                            <td class="text-end text-danger" id="rBiayaPengiriman">− Rp 0</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">↳ Biaya Lainnya</td>
                            <td class="text-end text-danger" id="rBiayaLainnya">− Rp 0</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold">Penghasilan Bersih</td>
                            <td class="text-end fw-bold text-primary" id="rPenghasilan">Rp 0</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">↳ Modal Barang</td>
                            <td class="text-end text-warning" id="rModal">− Rp 0</td>
                        </tr>
                        <tr class="border-top table-light">
                            <td class="fw-bold fs-6">LABA BERSIH</td>
                            <td class="text-end fw-bold fs-6" id="rLaba">Rp 0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Catatan</label>
                <textarea id="catatan" class="form-control" rows="2">{{ $penjualan->catatan }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary" id="btnUpdate">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
const existingItems = @json($penjualan->items);

$(document).ready(function() {

    function fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

    function hitungSemua() {
        let totalModal = 0, totalJual = 0;
        $('#tabelBarang tbody tr[data-id]').each(function() {
            totalModal += parseFloat($(this).data('subtotal-modal')) || 0;
            totalJual  += parseFloat($(this).data('subtotal-jual')) || 0;
        });

        const labaItem        = totalJual - totalModal;
        const biayaAdmin      = parseFloat($('#biaya_admin').val()) || 0;
        const biayaPengiriman = parseFloat($('#biaya_pengiriman').val()) || 0;
        const biayaLainnya    = parseFloat($('#biaya_lainnya').val()) || 0;
        const totalBiaya      = biayaAdmin + biayaPengiriman + biayaLainnya;
        const penghasilan     = totalJual - totalBiaya;
        const laba            = penghasilan - totalModal;

        $('#footModal').text(fmt(totalModal));
        $('#footJual').text(fmt(totalJual));
        $('#footLabaItem').text(fmt(labaItem)).toggleClass('text-success', labaItem >= 0).toggleClass('text-danger', labaItem < 0);

        $('#rTotalJual').text(fmt(totalJual));
        $('#rBiayaAdmin').text('− ' + fmt(biayaAdmin));
        $('#rBiayaPengiriman').text('− ' + fmt(biayaPengiriman));
        $('#rBiayaLainnya').text('− ' + fmt(biayaLainnya));
        $('#rPenghasilan').text(fmt(penghasilan));
        $('#rModal').text('− ' + fmt(totalModal));
        $('#rLaba').text(fmt(laba)).removeClass('text-success text-danger').addClass(laba >= 0 ? 'text-success' : 'text-danger');
    }

    // ====== LOAD BARANG ======
    Swal.fire({ title: 'Memuat data barang...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    let barangData = [];
    $.get("{{ route('select2.barangSemua') }}", function(data) {
        barangData = data.map(item => ({
            id:          item.id_barang,
            text:        (item.kode_barang ?? '') + ' - ' + item.nama_barang + ' (' + fmt(item.harga_jual) + ')',
            id_barang:   item.id_barang,
            kode_barang: item.kode_barang,
            nama_barang: item.nama_barang,
            harga_kulak: parseFloat(item.harga_kulak) || 0,
            harga_jual:  parseFloat(item.harga_jual) || 0,
            stok:        item.stok_barang ?? 0,
        }));

        $('#barangSelect').select2({
            placeholder: 'Cari barang dari master...',
            data: barangData,
            matcher: (params, data) => {
                if (!params.term) return data;
                return data.text.toLowerCase().includes(params.term.toLowerCase()) ? data : null;
            }
        });

        // Load existing items
        existingItems.forEach(function(ex) {
            const master = barangData.find(b => b.id_barang == ex.barang_id) || {};
            tambahBarang({
                id_barang:   ex.barang_id,
                kode_barang: ex.kode_barang ?? '',
                nama_barang: ex.nama_barang,
                harga_kulak: parseFloat(ex.harga_kulak) || 0,
                harga_jual:  parseFloat(ex.harga_jual) || 0,
                stok:        master.stok ?? 999,
            }, ex.qty, ex.harga_jual);
        });

        Swal.close();
    });

    $('#barangSelect').on('select2:select', function(e) {
        tambahBarang(e.params.data);
        $(this).val(null).trigger('change');
    });

    function tambahBarang(item, existingQty, existingHargaJual) {
        $('#emptyRow').remove();
        const existing = $(`#tabelBarang tbody tr[data-id="${item.id_barang}"]`);
        if (existing.length && !existingQty) {
            const qtyInput = existing.find('.qty');
            const newQty = parseInt(qtyInput.val()) + 1;
            if (newQty > item.stok) return Swal.fire('Stok tidak cukup!', `Stok: ${item.stok}`, 'warning');
            qtyInput.val(newQty);
            updateRow(existing);
            return;
        }

        const qty      = parseInt(existingQty) > 0 ? parseInt(existingQty) : 1;
        const hargaJual  = existingHargaJual || item.harga_jual || 0;
        const hargaKulak = item.harga_kulak || 0;
        // Stok saat ini sudah terpotong oleh qty transaksi ini sendiri (jika stok_dipotong),
        // jadi batas maksimal edit = stok sisa + qty yang sudah direservasi transaksi ini.
        const stokSisa = parseInt(item.stok);
        const maxQty   = Number.isFinite(stokSisa) ? Math.max(stokSisa, qty) : qty;

        const tr = $(`
            <tr data-id="${item.id_barang}" data-kode="${item.kode_barang ?? ''}"
                data-nama="${item.nama_barang}" data-kulak="${hargaKulak}"
                data-stok="${maxQty}" data-subtotal-modal="0" data-subtotal-jual="0">
                <td class="text-start">
                    <div class="fw-semibold">${item.nama_barang}</div>
                    <small class="text-muted">${item.kode_barang ?? ''}</small>
                </td>
                <td class="text-end text-muted">${fmt(hargaKulak)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm harga-jual text-end"
                           value="${hargaJual}" min="0" style="width:110px">
                </td>
                <td class="text-center">${stokSisa}</td>
                <td>
                    <input type="number" class="form-control form-control-sm qty text-center"
                           value="${qty}" min="1" max="${maxQty}" style="width:70px">
                </td>
                <td class="text-end sub-modal text-warning">Rp 0</td>
                <td class="text-end sub-jual text-primary fw-semibold">Rp 0</td>
                <td class="text-end laba-item fw-bold">Rp 0</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del-row"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
        `);
        $('#tabelBarang tbody').append(tr);
        updateRow(tr);
    }

    function updateRow(tr) {
        const kulak  = parseFloat(tr.data('kulak')) || 0;
        const jual   = parseFloat(tr.find('.harga-jual').val()) || 0;
        const qty    = parseInt(tr.find('.qty').val()) || 1;
        const stok   = parseInt(tr.data('stok'));

        if (qty > stok) {
            Swal.fire('Stok tidak cukup!', `Stok tersedia: ${stok}`, 'warning');
            tr.find('.qty').val(stok);
            return;
        }

        const subModal = kulak * qty;
        const subJual  = jual * qty;
        const labaItem = subJual - subModal;

        tr.data('subtotal-modal', subModal);
        tr.data('subtotal-jual', subJual);

        tr.find('.sub-modal').text(fmt(subModal));
        tr.find('.sub-jual').text(fmt(subJual));
        tr.find('.laba-item').text(fmt(labaItem))
          .removeClass('text-success text-danger')
          .addClass(labaItem >= 0 ? 'text-success' : 'text-danger');

        hitungSemua();
    }

    $('#tabelBarang').on('input', '.qty, .harga-jual', function() {
        updateRow($(this).closest('tr'));
    });

    $('#tabelBarang').on('click', '.del-row', function() {
        $(this).closest('tr').remove();
        if (!$('#tabelBarang tbody tr[data-id]').length) {
            $('#tabelBarang tbody').append('<tr id="emptyRow"><td colspan="9" class="text-center text-muted">Belum ada barang.</td></tr>');
        }
        hitungSemua();
    });

    $('#biaya_admin, #biaya_pengiriman, #biaya_lainnya').on('input', hitungSemua);

    // ====== UPDATE ======
    $('#btnUpdate').on('click', function() {
        const items = [];
        $('#tabelBarang tbody tr[data-id]').each(function() {
            const tr = $(this);
            items.push({
                id_barang:      tr.data('id'),
                kode_barang:    tr.data('kode'),
                nama_barang:    tr.data('nama'),
                harga_kulak:    parseFloat(tr.data('kulak')),
                harga_jual:     parseFloat(tr.find('.harga-jual').val()),
                qty:            parseInt(tr.find('.qty').val()),
                subtotal_modal: parseFloat(tr.data('subtotal-modal')),
                subtotal_jual:  parseFloat(tr.data('subtotal-jual')),
            });
        });

        if (!items.length) return Swal.fire('Perhatian!', 'Minimal tambahkan 1 barang.', 'warning');

        Swal.fire({
            title: 'Simpan perubahan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;

            $.ajax({
                url: '/penjualan-platform/{{ $penjualan->id }}',
                type: 'POST',
                data: {
                    _method:          'PUT',
                    platform:         $('#platform').val(),
                    nomor_pesanan:    $('#nomor_pesanan').val(),
                    nama_pembeli:     $('#nama_pembeli').val(),
                    tanggal:          $('#tanggal').val(),
                    status:           $('#status').val(),
                    biaya_admin:      $('#biaya_admin').val(),
                    biaya_pengiriman: $('#biaya_pengiriman').val(),
                    biaya_lainnya:    $('#biaya_lainnya').val(),
                    catatan:          $('#catatan').val(),
                    items:            JSON.stringify(items),
                    _token:           '{{ csrf_token() }}',
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        window.location.href = '/penjualan-platform/{{ $penjualan->id }}';
                    });
                },
                error: err => Swal.fire('Gagal!', err.responseJSON?.message ?? 'Terjadi kesalahan.', 'error'),
            });
        });
    });
});
</script>
@endpush
