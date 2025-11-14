  $(document).on('click', '.btn-toggle-detail', function() {
    const btn = $(this);
    const id = btn.data('id');
    const tr = btn.closest('tr');

    // Jika sudah terbuka, tutup
    if (tr.next().hasClass('detail-row')) {
        tr.next().toggle();
        return;
    }

    // Tampilkan indikator loading
    const loadingRow = `
        <tr class="detail-row">
            <td colspan="12" class="text-center text-muted py-3">Memuat detail...</td>
        </tr>`;
    tr.after(loadingRow);

    // Ambil data dari backend
    $.get(`detail/service/shows/${id}`, function(res) {

        console.log("data.e", res)
        // Buat tabel job
        let jobRows = res.jobs.length
            ? res.jobs.map(j => `
                <tr>
                    <td>${j.jasa?.nama_jasa || '-'}</td>
                    <td>${j.qty}</td>
                    <td>${formatRupiah(j.price)}</td>
                    <td>${formatRupiah(j.subtotal)}</td>
                </tr>
              `).join('')
            : `<tr><td colspan="4" class="text-center text-muted">Tidak ada jasa</td></tr>`;

        // Buat tabel sparepart
        let spareRows = res.spareparts.length
            ? res.spareparts.map(sp => `
                <tr>
                    <td>${sp.barang?.id_barang || '-'} - ${sp.barang?.kode_barang || '-'} - ${sp.barang?.nama_barang || '-'} - ${sp.barang?.merk_barang || '-'} - ${sp.barang?.merk_barang || '-'} - ${sp.barang?.keterangan || '-'}</td>
                    <td>${sp.qty}</td>
                    <td>${formatRupiah(sp.price)}</td>
                    <td>${formatRupiah(sp.subtotal)}</td>
                </tr>
              `).join('')
            : `<tr><td colspan="4" class="text-center text-muted">Tidak ada sparepart</td></tr>`;

        // Total semua
      let totalJasa = res.jobs.reduce((a, b) => a + parseFloat(b.subtotal || 0), 0);
let totalSpare = res.spareparts.reduce((a, b) => a + parseFloat(b.subtotal || 0), 0);
let totalAll = totalJasa + totalSpare;


        const detailHtml = `
        <tr class="detail-row">
            <td colspan="12" class="bg-light">
                <div class="p-3 text-start">
                 <table class="table table-sm table-bordered mb-3">
                        <thead><tr><th>tanggal Service</th><th>tanggal selesai</th><th>jatuh tempo</th></tr></thead>
                        <tbody><tr><td>${res.service_date} </td><td>${res.estimate_date}</td><td>${res.due_date}</td></tr></tbody>
                    </table>
                    <h6 class="fw-bold mb-2">🧰 Jasa / Service Job</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <thead><tr><th>Nama Jasa</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
                        <tbody>${jobRows}</tbody>
                    </table>

                    <h6 class="fw-bold mb-2">🔩 Sparepart / Barang</h6>
                    <table class="table table-sm table-bordered mb-3">
                        <thead><tr><th>Nama Barang</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
                        <tbody>${spareRows}</tbody>
                    </table>

                    <div class="text-end">
                        <strong>Total Jasa:</strong> ${formatRupiah(totalJasa)} <br>
                        <strong>Total Sparepart:</strong> ${formatRupiah(totalSpare)} <br>
                        <strong>Grand Total:</strong> <span class="text-success">${formatRupiah(totalAll)}</span>
                    </div>
                </div>
            </td>
        </tr>`;

        tr.next('.detail-row').remove();
        tr.after(detailHtml);
    }).fail(function() {
        tr.next('.detail-row').html('<td colspan="11" class="text-danger text-center py-3">Gagal memuat detail</td>');
    });
});
