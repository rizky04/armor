@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Edit Transaksi Draft</h4>
        <h6>{{ $transaksi->nomor_transaksi }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('transaksi-motor.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <!-- INFO CUSTOMER -->
        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-user me-1"></i> Info Customer</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                <input type="text" id="nama_customer" class="form-control" value="{{ $transaksi->nama_customer }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">No. HP</label>
                <input type="text" id="no_hp" class="form-control" value="{{ $transaksi->no_hp }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Plat Nomor</label>
                <input type="text" id="plat_nomor" class="form-control" value="{{ $transaksi->plat_nomor }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Nama Motor</label>
                <input type="text" id="nama_motor" class="form-control" value="{{ $transaksi->nama_motor }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="tanggal" class="form-control" value="{{ $transaksi->tanggal }}">
            </div>
        </div>

        <!-- SECTION BARANG -->
        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-box me-1"></i> Barang / Sparepart</h6>
        <div class="row mb-2">
            <div class="col-md-10">
                <select id="barangSelect" style="width:100%;" placeholder="Cari barang..."><option></option></select>
            </div>
            <div class="col-md-2">
                <input type="text" id="scanBarcode" class="form-control" placeholder="Scan barcode...">
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center" id="tabelBarang">
                <thead class="table-light">
                    <tr>
                        <th style="width:40%">Nama Barang</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th style="width:10%">Qty</th>
                        <th>Subtotal</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Total Barang</td>
                        <td colspan="2" class="fw-bold text-end" id="totalBarang">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- SECTION JASA -->
        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-wrench me-1"></i> Jasa / Pekerjaan</h6>
        <div class="row mb-2">
            <div class="col-md-10">
                <select id="jasaSelect" style="width:100%;" placeholder="Cari dari master jasa..."><option></option></select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-warning w-100" id="btnToggleManualJasa">
                    <i class="fa-solid fa-pen me-1"></i> Manual
                </button>
            </div>
        </div>
        <div class="row mb-2 g-2" id="rowManualJasa" style="display:none;">
            <div class="col-md-6">
                <input type="text" id="manualNamaJasa" class="form-control" placeholder="Nama jasa...">
            </div>
            <div class="col-md-3">
                <input type="number" id="manualHargaJasa" class="form-control" placeholder="Harga (Rp)" min="0">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-warning w-100" id="btnAddManualJasa">
                    <i class="fa-solid fa-plus me-1"></i> Tambahkan
                </button>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered text-center" id="tabelJasa">
                <thead class="table-light">
                    <tr>
                        <th style="width:50%">Nama Jasa</th>
                        <th>Harga</th>
                        <th style="width:10%">Qty</th>
                        <th>Subtotal</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Jasa</td>
                        <td colspan="2" class="fw-bold text-end" id="totalJasa">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- DISKON & GRAND TOTAL -->
        <div class="d-flex justify-content-end mb-3">
            <div class="text-end" style="min-width:320px;">
                <div class="fs-5">Total Barang: <strong id="grandBarang">Rp 0</strong></div>
                <div class="fs-5">Total Jasa: <strong id="grandJasa">Rp 0</strong></div>
                <div class="d-flex justify-content-end align-items-center gap-2 my-2">
                    <label class="form-label mb-0" for="diskon">Diskon (Rp)</label>
                    <input type="number" id="diskon" class="form-control form-control-sm text-end" value="{{ $transaksi->diskon }}" min="0" style="width:150px">
                </div>
                <div class="fs-4 text-primary">Grand Total: <strong id="grandTotal">Rp 0</strong></div>
            </div>
        </div>

        <!-- PEMBAYARAN & CATATAN -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="fa-solid fa-credit-card me-1"></i> Metode Pembayaran <span class="text-danger">*</span></label>
                <div class="d-flex gap-2 flex-wrap mt-1">
                    @foreach([['cash','Cash','fa-money-bill-wave','success'],['transfer','Transfer','fa-building-columns','primary'],['qris','QRIS','fa-qrcode','warning'],['debit','Debit','fa-credit-card','info']] as [$val,$label,$icon,$color])
                    <div>
                        <input type="radio" class="btn-check" name="metode_pembayaran" id="mp_{{ $val }}" value="{{ $val }}"
                               {{ $transaksi->metode_pembayaran === $val ? 'checked' : '' }}>
                        <label class="btn btn-outline-{{ $color }} btn-sm" for="mp_{{ $val }}">
                            <i class="fa-solid {{ $icon }} me-1"></i>{{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Catatan</label>
                <textarea id="catatan" class="form-control" rows="2">{{ $transaksi->catatan }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary" id="btnSimpanDraft">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Draft
            </button>
            <button class="btn btn-success" id="btnSelesai">
                <i class="fa-solid fa-check me-1"></i> Selesaikan & Simpan
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<audio id="beep" src="https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg"></audio>

{{-- Data existing dari server --}}
<script>
const existingBarang = @json($transaksi->barangs);
const existingJasa   = @json($transaksi->jasas);
</script>

<script>
$(document).ready(function () {

    function fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

    // ====== HITUNG TOTAL ======
    function hitungTotal() {
        let tb = 0, tj = 0;
        $('#tabelBarang tbody tr[data-id]').each(function () {
            const harga = parseFloat($(this).find('.harga').val()) || 0;
            const qty   = parseInt($(this).find('.qty').val()) || 0;
            tb += harga * qty;
        });
        $('#tabelJasa tbody tr[data-id]').each(function () {
            const harga = parseFloat($(this).find('.harga-jasa').val()) || 0;
            const qty   = parseInt($(this).find('.qty-jasa').val()) || 0;
            tj += harga * qty;
        });
        const diskon = parseFloat($('#diskon').val()) || 0;
        $('#totalBarang, #grandBarang').text(fmt(tb));
        $('#totalJasa, #grandJasa').text(fmt(tj));
        $('#grandTotal').text(fmt(tb + tj - diskon));
    }

    $('#diskon').on('input', hitungTotal);

    // ====== TAMBAH BARIS BARANG ======
    function tambahBarang(item) {
        const existing = $(`#tabelBarang tbody tr[data-id="${item.id_barang}"]`);
        if (existing.length) {
            const qtyInput = existing.find('.qty');
            const newQty = parseInt(qtyInput.val()) + 1;
            if (newQty > item.stok) return Swal.fire('Stok tidak cukup!', `Stok: ${item.stok}`, 'warning');
            qtyInput.val(newQty);
            updateBarangRow(existing);
            return;
        }
        const harga = parseFloat(item.harga) || 0;
        const qty   = parseInt(item.qty) > 0 ? parseInt(item.qty) : 1;
        const stok  = parseInt(item.stok);
        const maxQty = Number.isFinite(stok) ? Math.max(stok, qty) : qty;
        const tr = $(`
            <tr data-id="${item.id_barang}" data-kode="${item.kode_barang ?? ''}"
                data-nama="${item.nama_barang}"
                data-harga-kulak="${item.harga_kulak ?? 0}" data-stok="${item.stok}">
                <td class="text-start">${item.kode_barang ?? ''} ${item.kode_barang ? '-' : ''} ${item.nama_barang}</td>
                <td><input type="number" value="${harga}" min="0" class="form-control form-control-sm harga text-end" style="width:110px"></td>
                <td>${item.stok}</td>
                <td><input type="number" value="${qty}" min="1" max="${maxQty}" class="form-control form-control-sm qty text-center"></td>
                <td class="sub" data-val="${harga * qty}">${fmt(harga * qty)}</td>
                <td><button type="button" class="btn btn-sm btn-danger del-barang"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `);
        $('#tabelBarang tbody').append(tr);
        hitungTotal();
    }

    function updateBarangRow(tr) {
        const harga = parseFloat(tr.find('.harga').val()) || 0;
        const qty   = parseInt(tr.find('.qty').val()) || 1;
        const stok  = parseInt(tr.attr('data-stok'));
        if (qty > stok) {
            Swal.fire('Stok tidak cukup!', `Stok: ${stok}`, 'warning');
            tr.find('.qty').val(stok);
            return;
        }
        const sub = harga * qty;
        tr.find('.sub').data('val', sub).text(fmt(sub));
        hitungTotal();
    }

    $('#tabelBarang').on('input', '.qty, .harga', function () { updateBarangRow($(this).closest('tr')); });
    $('#tabelBarang').on('click', '.del-barang', function () {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    // ====== TAMBAH BARIS JASA ======
    function tambahJasa(item) {
        // Manual bila ditandai is_manual, atau data lama tanpa id jasa (jasa_id kosong)
        const jasaRefId = item.id_jasa ?? item.jasa_id ?? null;
        const isManualBool = item.is_manual ? true : (jasaRefId == null);
        const isManual = isManualBool ? '1' : '0';
        const jasaId   = isManualBool ? ('manual_' + Date.now() + '_' + Math.floor(Math.random() * 1000)) : jasaRefId;
        const harga    = parseFloat(item.harga ?? item.harga_jasa) || 0;
        const qty      = parseInt(item.qty) > 0 ? parseInt(item.qty) : 1;

        const existing = $(`#tabelJasa tbody tr[data-id="${jasaId}"]`);
        if (!isManualBool && existing.length) {
            existing.find('.qty-jasa').val(parseInt(existing.find('.qty-jasa').val()) + 1);
            updateJasaRow(existing);
            return;
        }

        const badge = item.is_manual ? ' <span class="badge bg-warning text-dark ms-1">manual</span>' : '';
        const tr = $(`
            <tr data-id="${jasaId}" data-kode="${item.kode_jasa ?? ''}" data-nama="${item.nama_jasa}"
                data-manual="${isManual}">
                <td class="text-start">${item.kode_jasa ? item.kode_jasa + ' - ' : ''}${item.nama_jasa}${badge}</td>
                <td><input type="number" value="${harga}" min="0" class="form-control form-control-sm harga-jasa text-end" style="width:110px"></td>
                <td><input type="number" value="${qty}" min="1" class="form-control form-control-sm qty-jasa text-center"></td>
                <td class="sub-jasa" data-val="${harga * qty}">${fmt(harga * qty)}</td>
                <td><button type="button" class="btn btn-sm btn-danger del-jasa"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `);
        $('#tabelJasa tbody').append(tr);
        hitungTotal();
    }

    // ====== JASA MANUAL ======
    $('#btnToggleManualJasa').on('click', function () {
        $('#rowManualJasa').toggle();
        $(this).toggleClass('btn-outline-warning btn-warning');
    });

    $('#btnAddManualJasa').on('click', function () {
        const nama  = $('#manualNamaJasa').val().trim();
        const harga = parseFloat($('#manualHargaJasa').val()) || 0;
        if (!nama)      return Swal.fire('Perhatian!', 'Nama jasa wajib diisi.', 'warning');
        if (harga <= 0) return Swal.fire('Perhatian!', 'Harga harus lebih dari 0.', 'warning');
        tambahJasa({ id_jasa: null, kode_jasa: '', nama_jasa: nama, harga: harga, is_manual: true });
        $('#manualNamaJasa').val('').focus();
        $('#manualHargaJasa').val('');
    });

    $('#manualHargaJasa').on('keypress', function (e) {
        if (e.which === 13) $('#btnAddManualJasa').trigger('click');
    });

    function updateJasaRow(tr) {
        const harga = parseFloat(tr.find('.harga-jasa').val()) || 0;
        const qty   = parseInt(tr.find('.qty-jasa').val()) || 1;
        const sub   = harga * qty;
        tr.find('.sub-jasa').data('val', sub).text(fmt(sub));
        hitungTotal();
    }

    $('#tabelJasa').on('input', '.qty-jasa, .harga-jasa', function () { updateJasaRow($(this).closest('tr')); });
    $('#tabelJasa').on('click', '.del-jasa', function () {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    // ====== LOAD DATA BARANG & ISI EXISTING ======
    Swal.fire({ title: 'Memuat data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    let barangData = [];
    $.get("{{ route('select2.barangSemua') }}", function (data) {
        barangData = data.map(item => ({
            id: item.id_barang, text: item.kode_barang + ' - ' + item.nama_barang,
            id_barang: item.id_barang, kode_barang: item.kode_barang,
            nama_barang: item.nama_barang, harga: item.harga_jual,
            harga_kulak: item.harga_kulak ?? 0, stok: item.stok_barang ?? 0,
        }));

        $('#barangSelect').select2({
            placeholder: 'Cari barang...',
            data: barangData,
            matcher: (params, data) => !params.term || data.text.toLowerCase().includes(params.term.toLowerCase()) ? data : null,
        });

        // Isi barang existing — gunakan stok dari master + qty existing
        existingBarang.forEach(function (ex) {
            const master = barangData.find(b => b.id_barang == ex.barang_id);
            tambahBarang({
                id_barang:   ex.barang_id,
                kode_barang: ex.kode_barang,
                nama_barang: ex.nama_barang,
                harga:       ex.harga,
                harga_kulak: ex.harga_kulak,
                stok:        master ? master.stok : 9999,
                qty:         ex.qty,
            });
        });

        Swal.close();
    }).fail(() => Swal.close());

    $('#barangSelect').on('select2:select', function (e) {
        tambahBarang(e.params.data);
        $(this).val(null).trigger('change');
    });

    // ====== SCAN BARCODE ======
    $('#scanBarcode').on('keypress', function (e) {
        if (e.which !== 13) return;
        const kode = $(this).val().trim();
        if (!kode) return;
        $(this).val('');
        fetch(`/api/barang/by-qr/${kode}`)
            .then(r => r.json())
            .then(item => {
                if (item?.id_barang) tambahBarang({
                    id_barang: item.id_barang, kode_barang: item.kode_barang,
                    nama_barang: item.nama_barang, harga: item.harga_jual,
                    harga_kulak: item.harga_kulak ?? 0, stok: item.stok_barang ?? 0,
                });
                else Swal.fire('Barang tidak ditemukan!', `Kode: ${kode}`, 'warning');
            });
    });

    // ====== LOAD JASA SELECT2 & ISI EXISTING ======
    $('#jasaSelect').select2({
        placeholder: 'Cari jasa...',
        ajax: {
            url: "{{ route('select2.jasa') }}",
            dataType: 'json', delay: 200,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id_jasa, text: (item.kode_jasa ? item.kode_jasa + ' - ' : '') + item.nama_jasa,
                    id_jasa: item.id_jasa, kode_jasa: item.kode_jasa,
                    nama_jasa: item.nama_jasa, harga: item.harga_jasa,
                }))
            })
        },
    });

    $('#jasaSelect').on('select2:select', function (e) {
        tambahJasa(e.params.data);
        $(this).val(null).trigger('change');
    });

    // Isi jasa existing
    existingJasa.forEach(ex => tambahJasa(ex));

    // ====== COLLECT & SIMPAN ======
    function collectPayload(status) {
        const barangItems = [];
        $('#tabelBarang tbody tr[data-id]').each(function () {
            const tr    = $(this);
            const harga = parseFloat(tr.find('.harga').val()) || 0;
            const qty   = parseInt(tr.find('.qty').val()) || 0;
            barangItems.push({
                id_barang:   tr.attr('data-id'),
                kode_barang: tr.attr('data-kode'),
                nama_barang: tr.attr('data-nama'),
                harga:       harga,
                harga_kulak: parseFloat(tr.attr('data-harga-kulak')) || 0,
                qty:         qty,
                subtotal:    harga * qty,
            });
        });
        const jasaItems = [];
        $('#tabelJasa tbody tr[data-id]').each(function () {
            const tr       = $(this);
            const isManual = tr.attr('data-manual') == '1';
            const harga    = parseFloat(tr.find('.harga-jasa').val()) || 0;
            const qty      = parseInt(tr.find('.qty-jasa').val()) || 0;
            jasaItems.push({
                id_jasa:   isManual ? null : tr.attr('data-id'),
                kode_jasa: tr.attr('data-kode'),
                nama_jasa: tr.attr('data-nama'),
                harga:     harga,
                qty:       qty,
                subtotal:  harga * qty,
                is_manual: isManual,
            });
        });
        return {
            nama_customer:      $('#nama_customer').val().trim(),
            no_hp:              $('#no_hp').val().trim(),
            plat_nomor:         $('#plat_nomor').val().trim(),
            nama_motor:         $('#nama_motor').val().trim(),
            tanggal:            $('#tanggal').val(),
            catatan:            $('#catatan').val().trim(),
            status:             status,
            metode_pembayaran:  $('input[name="metode_pembayaran"]:checked').val(),
            diskon:             parseFloat($('#diskon').val()) || 0,
            barang_items:       JSON.stringify(barangItems),
            jasa_items:         JSON.stringify(jasaItems),
            _token:             '{{ csrf_token() }}',
            _method:            'PUT',
        };
    }

    function simpan(status, label) {
        const payload = collectPayload(status);
        if (!payload.nama_customer) return Swal.fire('Perhatian!', 'Nama customer wajib diisi.', 'warning');
        if (!payload.tanggal)       return Swal.fire('Perhatian!', 'Tanggal wajib diisi.', 'warning');

        const b = JSON.parse(payload.barang_items);
        const j = JSON.parse(payload.jasa_items);
        if (!b.length && !j.length) return Swal.fire('Perhatian!', 'Minimal tambahkan 1 barang atau 1 jasa.', 'warning');

        Swal.fire({
            title: label,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: "{{ route('transaksi-motor.update', $transaksi->id) }}",
                type: 'POST',
                data: payload,
                success: res => {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        window.location.href = "{{ route('transaksi-motor.index') }}";
                    });
                },
                error: err => Swal.fire('Gagal!', err.responseJSON?.message ?? 'Terjadi kesalahan.', 'error'),
            });
        });
    }

    $('#btnSimpanDraft').on('click', () => simpan('draft', 'Simpan sebagai Draft?'));
    $('#btnSelesai').on('click', () => simpan('selesai', 'Selesaikan transaksi? Stok barang akan dipotong.'));
});
</script>
@endpush
