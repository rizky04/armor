@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Transaksi Motor</h4>
        <h6>Buat Transaksi Baru</h6>
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
                <input type="text" id="nama_customer" class="form-control" placeholder="Nama customer">
            </div>
            <div class="col-md-2">
                <label class="form-label">No. HP</label>
                <input type="text" id="no_hp" class="form-control" placeholder="08xx">
            </div>
            <div class="col-md-2">
                <label class="form-label">Plat Nomor</label>
                <input type="text" id="plat_nomor" class="form-control" placeholder="M 1234 AB">
            </div>
            <div class="col-md-2">
                <label class="form-label">Nama Motor</label>
                <input type="text" id="nama_motor" class="form-control" placeholder="Vespa Sprint">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <!-- SECTION BARANG -->
        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-box me-1"></i> Barang / Sparepart</h6>
        <div class="row mb-2">
            <div class="col-md-10">
                <select id="barangSelect" style="width:100%;" placeholder="Cari barang...">
                    <option></option>
                </select>
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
                <tbody>
                    <tr id="emptyBarang"><td colspan="6" class="text-muted">Belum ada barang ditambahkan.</td></tr>
                </tbody>
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
                <select id="jasaSelect" style="width:100%;" placeholder="Cari dari master jasa...">
                    <option></option>
                </select>
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
                <tbody>
                    <tr id="emptyJasa"><td colspan="5" class="text-muted">Belum ada jasa ditambahkan.</td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Jasa</td>
                        <td colspan="2" class="fw-bold text-end" id="totalJasa">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- GRAND TOTAL -->
        <div class="d-flex justify-content-end mb-3">
            <div class="text-end">
                <div class="fs-5">Total Barang: <strong id="grandBarang">Rp 0</strong></div>
                <div class="fs-5">Total Jasa: <strong id="grandJasa">Rp 0</strong></div>
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
                        <input type="radio" class="btn-check" name="metode_pembayaran" id="mp_{{ $val }}" value="{{ $val }}" {{ $val === 'cash' ? 'checked' : '' }}>
                        <label class="btn btn-outline-{{ $color }} btn-sm" for="mp_{{ $val }}">
                            <i class="fa-solid {{ $icon }} me-1"></i>{{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <label class="form-label">Catatan</label>
                <textarea id="catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary" id="btnDraft">Simpan Draft</button>
            <button class="btn btn-success" id="btnSelesai">Selesaikan & Simpan</button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<audio id="beep" src="https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg"></audio>

<script>
$(document).ready(function () {

    // ====== FORMAT RUPIAH ======
    function fmt(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // ====== HITUNG TOTAL ======
    function hitungTotal() {
        let tb = 0, tj = 0;
        $('#tabelBarang tbody tr[data-id]').each(function () {
            tb += parseFloat($(this).find('.sub').data('val')) || 0;
        });
        $('#tabelJasa tbody tr[data-id]').each(function () {
            tj += parseFloat($(this).find('.sub-jasa').data('val')) || 0;
        });
        $('#totalBarang, #grandBarang').text(fmt(tb));
        $('#totalJasa, #grandJasa').text(fmt(tj));
        $('#grandTotal').text(fmt(tb + tj));
    }

    // ====== BARANG SELECT2 ======
    Swal.fire({ title: 'Memuat data barang...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    let barangData = [];
    $.get("{{ route('select2.barangSemua') }}", function (data) {
        barangData = data.map(item => ({
            id: item.id_barang,
            text: item.kode_barang + ' - ' + item.nama_barang + ' (' + fmt(item.harga_jual) + ')',
            id_barang: item.id_barang,
            kode_barang: item.kode_barang,
            nama_barang: item.nama_barang,
            harga: item.harga_jual,
            harga_kulak: item.harga_kulak ?? 0,
            stok: item.stok_barang ?? 0,
        }));

        $('#barangSelect').select2({
            placeholder: 'Cari barang...',
            data: barangData,
            matcher: function (params, data) {
                if (!params.term) return data;
                return data.text.toLowerCase().includes(params.term.toLowerCase()) ? data : null;
            }
        });
        Swal.close();
    }).fail(() => Swal.close());

    $('#barangSelect').on('select2:select', function (e) {
        tambahBarang(e.params.data);
        $(this).val(null).trigger('change');
    });

    function tambahBarang(item) {
        $('#emptyBarang').remove();
        const existing = $(`#tabelBarang tbody tr[data-id="${item.id_barang}"]`);
        if (existing.length) {
            const qtyInput = existing.find('.qty');
            const newQty = parseInt(qtyInput.val()) + 1;
            if (newQty > item.stok) {
                return Swal.fire('Stok tidak cukup!', `Stok tersedia: ${item.stok}`, 'warning');
            }
            qtyInput.val(newQty);
            updateBarangRow(existing);
            return;
        }

        const tr = $(`
            <tr data-id="${item.id_barang}" data-kode="${item.kode_barang}" data-nama="${item.nama_barang}"
                data-harga="${item.harga}" data-harga-kulak="${item.harga_kulak ?? 0}" data-stok="${item.stok}">
                <td class="text-start">${item.kode_barang} - ${item.nama_barang}</td>
                <td class="harga">${fmt(item.harga)}</td>
                <td>${item.stok}</td>
                <td><input type="number" value="1" min="1" max="${item.stok}" class="form-control form-control-sm qty text-center"></td>
                <td class="sub" data-val="${item.harga}">${fmt(item.harga)}</td>
                <td><button type="button" class="btn btn-sm btn-danger del-barang"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `);
        $('#tabelBarang tbody').append(tr);
        hitungTotal();
        document.getElementById('beep').play();
    }

    $('#tabelBarang').on('input', '.qty', function () {
        updateBarangRow($(this).closest('tr'));
    });

    function updateBarangRow(tr) {
        const harga = parseFloat(tr.data('harga'));
        const qty   = parseInt(tr.find('.qty').val()) || 1;
        const stok  = parseInt(tr.data('stok'));

        if (qty > stok) {
            Swal.fire('Stok tidak cukup!', `Stok tersedia: ${stok}`, 'warning');
            tr.find('.qty').val(stok);
            return;
        }
        const sub = harga * qty;
        tr.find('.sub').data('val', sub).text(fmt(sub));
        hitungTotal();
    }

    $('#tabelBarang').on('click', '.del-barang', function () {
        $(this).closest('tr').remove();
        if (!$('#tabelBarang tbody tr[data-id]').length) {
            $('#tabelBarang tbody').append('<tr id="emptyBarang"><td colspan="6" class="text-muted">Belum ada barang ditambahkan.</td></tr>');
        }
        hitungTotal();
    });

    // ====== SCAN BARCODE ======
    $('#scanBarcode').on('keypress', function (e) {
        if (e.which === 13) {
            const kode = $(this).val().trim();
            if (!kode) return;
            $(this).val('');
            fetch(`/api/barang/by-qr/${kode}`)
                .then(r => r.json())
                .then(item => {
                    if (item && item.id_barang) {
                        tambahBarang({
                            id_barang: item.id_barang,
                            kode_barang: item.kode_barang,
                            nama_barang: item.nama_barang,
                            harga: item.harga_jual,
                            harga_kulak: item.harga_kulak ?? 0,
                            stok: item.stok_barang ?? 0,
                        });
                    } else {
                        Swal.fire('Barang tidak ditemukan!', `Kode: ${kode}`, 'warning');
                    }
                });
        }
    });

    // ====== JASA SELECT2 ======
    $('#jasaSelect').select2({
        placeholder: 'Cari jasa...',
        ajax: {
            url: "{{ route('select2.jasa') }}",
            dataType: 'json',
            delay: 200,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id_jasa,
                    text: (item.kode_jasa ? item.kode_jasa + ' - ' : '') + item.nama_jasa + ' (' + fmt(item.harga_jasa) + ')',
                    id_jasa: item.id_jasa,
                    kode_jasa: item.kode_jasa,
                    nama_jasa: item.nama_jasa,
                    harga: item.harga_jasa,
                }))
            })
        },
        minimumInputLength: 0,
    });

    // Langsung muat semua jasa saat focus
    $('#jasaSelect').on('select2:open', function () {
        $('.select2-search__field').trigger('input');
    });

    $('#jasaSelect').on('select2:select', function (e) {
        tambahJasa(e.params.data);
        $(this).val(null).trigger('change');
    });

    function tambahJasa(item) {
        $('#emptyJasa').remove();
        const isManual = item.is_manual ? '1' : '0';
        const rowId    = item.is_manual ? ('manual_' + Date.now()) : item.id_jasa;

        const existing = $(`#tabelJasa tbody tr[data-id="${item.id_jasa}"]`);
        if (!item.is_manual && existing.length) {
            const qtyInput = existing.find('.qty-jasa');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            updateJasaRow(existing);
            return;
        }

        const badge = item.is_manual ? ' <span class="badge bg-warning text-dark ms-1">manual</span>' : '';
        const tr = $(`
            <tr data-id="${rowId}" data-kode="${item.kode_jasa ?? ''}" data-nama="${item.nama_jasa}"
                data-harga="${item.harga}" data-manual="${isManual}">
                <td class="text-start">${item.kode_jasa ? item.kode_jasa + ' - ' : ''}${item.nama_jasa}${badge}</td>
                <td>${fmt(item.harga)}</td>
                <td><input type="number" value="1" min="1" class="form-control form-control-sm qty-jasa text-center"></td>
                <td class="sub-jasa" data-val="${item.harga}">${fmt(item.harga)}</td>
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
        if (!nama)     return Swal.fire('Perhatian!', 'Nama jasa wajib diisi.', 'warning');
        if (harga <= 0) return Swal.fire('Perhatian!', 'Harga harus lebih dari 0.', 'warning');
        tambahJasa({ id_jasa: null, kode_jasa: '', nama_jasa: nama, harga: harga, is_manual: true });
        $('#manualNamaJasa').val('').focus();
        $('#manualHargaJasa').val('');
    });

    $('#manualHargaJasa').on('keypress', function (e) {
        if (e.which === 13) $('#btnAddManualJasa').trigger('click');
    });

    $('#tabelJasa').on('input', '.qty-jasa', function () {
        updateJasaRow($(this).closest('tr'));
    });

    function updateJasaRow(tr) {
        const harga = parseFloat(tr.data('harga'));
        const qty   = parseInt(tr.find('.qty-jasa').val()) || 1;
        const sub   = harga * qty;
        tr.find('.sub-jasa').data('val', sub).text(fmt(sub));
        hitungTotal();
    }

    $('#tabelJasa').on('click', '.del-jasa', function () {
        $(this).closest('tr').remove();
        if (!$('#tabelJasa tbody tr[data-id]').length) {
            $('#tabelJasa tbody').append('<tr id="emptyJasa"><td colspan="5" class="text-muted">Belum ada jasa ditambahkan.</td></tr>');
        }
        hitungTotal();
    });

    // ====== SIMPAN ======
    function collectData(status) {
        const barangItems = [];
        $('#tabelBarang tbody tr[data-id]').each(function () {
            const tr = $(this);
            barangItems.push({
                id_barang:   tr.data('id'),
                kode_barang: tr.data('kode'),
                nama_barang: tr.data('nama'),
                harga:       parseFloat(tr.data('harga')),
                harga_kulak: parseFloat(tr.data('harga-kulak')) || 0,
                qty:         parseInt(tr.find('.qty').val()),
                subtotal:    parseFloat(tr.find('.sub').data('val')),
            });
        });

        const jasaItems = [];
        $('#tabelJasa tbody tr[data-id]').each(function () {
            const tr = $(this);
            const isManual = tr.data('manual') == '1';
            jasaItems.push({
                id_jasa:   isManual ? null : tr.data('id'),
                kode_jasa: tr.data('kode'),
                nama_jasa: tr.data('nama'),
                harga:     parseFloat(tr.data('harga')),
                qty:       parseInt(tr.find('.qty-jasa').val()),
                subtotal:  parseFloat(tr.find('.sub-jasa').data('val')),
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
            barang_items:       JSON.stringify(barangItems),
            jasa_items:         JSON.stringify(jasaItems),
            _token:             '{{ csrf_token() }}',
        };
    }

    function simpan(status) {
        const payload = collectData(status);

        if (!payload.nama_customer) {
            return Swal.fire('Perhatian!', 'Nama customer wajib diisi.', 'warning');
        }
        if (!payload.tanggal) {
            return Swal.fire('Perhatian!', 'Tanggal wajib diisi.', 'warning');
        }

        const barang = JSON.parse(payload.barang_items);
        const jasa   = JSON.parse(payload.jasa_items);
        if (!barang.length && !jasa.length) {
            return Swal.fire('Perhatian!', 'Minimal tambahkan 1 barang atau 1 jasa.', 'warning');
        }

        const label = status === 'selesai' ? 'Selesaikan & simpan transaksi?' : 'Simpan sebagai Draft?';
        Swal.fire({
            title: label,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('transaksi-motor.store') }}",
                type: 'POST',
                data: payload,
                success: function (res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#btnSelesai, #btnDraft').prop('disabled', true);

                        // Tambah tombol print
                        if ($('#btnPrint').length === 0) {
                            $('.d-flex.justify-content-end.gap-2').prepend(`
                                <a href="/transaksi-motor/${res.data.id}/print" target="_blank" class="btn btn-info" id="btnPrint">
                                    <i class="fa-solid fa-print me-1"></i> Print Nota
                                </a>
                            `);
                        }
                    });
                },
                error: function (err) {
                    Swal.fire('Gagal!', err.responseJSON?.message ?? 'Terjadi kesalahan.', 'error');
                },
            });
        });
    }

    $('#btnSelesai').on('click', () => simpan('selesai'));
    $('#btnDraft').on('click', () => simpan('draft'));
});
</script>
@endpush
