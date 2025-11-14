// ===================================================
// 1️⃣ TAMPILKAN MODAL SALES PAYMENT
// ===================================================
$(document).on('click', '.btn-create-sales-payment', function() {
    let id = $(this).data('id');
    let nomorSales = $(this).data('nomor');

    // Reset form dan modal
    $('#form-create-sales-payment')[0].reset();
    $('#payment_sales_id').val(id);
    $('#reference-sales').val(nomorSales || '-');
    $('#payment-detail-items').html('');
    $('#sisa-bayar-sales').text('Rp 0');
    $('#input-bayar-sales').val('');
    $('#kembalian-sales').val('Rp 0');
    $('#btn-print-sales-payment').addClass('d-none');

    $('#createSalesPayment').modal('show');

    // Ambil data sales
    $.ajax({
        url: `/sales/${id}/payment-detail`,
        type: 'GET',
        success: function(res) {
            if (res.status) {
                const data = res.data;

                // ==== Isi Tabel Barang ====
                let itemsHtml = '';
                data.items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td>${item.barang?.nama_barang ?? '-'}</td>
                            <td class="text-center">${item.qty}</td>
                            <td class="text-end">Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                });
                $('#payment-detail-items').html(itemsHtml);

                // ==== Total & Sisa Bayar ====
                const sisa = Number(data.sisa_bayar);
                $('#sisa-bayar-sales').text(`Rp ${sisa.toLocaleString('id-ID')}`);

                // ==== Otomatis isi nominal bayar ====
                $('#input-bayar-sales').val(sisa.toLocaleString('id-ID'));

                $('#kembalian-sales').val('Rp 0');
            }
        },
        error: function() {
            Swal.fire('Error', 'Gagal memuat data sales.', 'error');
        }
    });
});

// ===================================================
// 2️⃣ FORMAT INPUT DAN HITUNG KEMBALIAN + VALIDASI
// ===================================================
$(document).on('input', '#input-bayar-sales', function() {
    let value = $(this).val().replace(/[^\d]/g, '');
    $(this).val(value ? Number(value).toLocaleString('id-ID') : '');

    const total = parseFloat($('#sisa-bayar-sales').text().replace(/[^\d]/g, '')) || 0;
    const bayar = parseFloat(value) || 0;
    const kembali = bayar - total;

    $('#kembalian-sales').val(kembali > 0 ? `Rp ${kembali.toLocaleString('id-ID')}` : 'Rp 0');

    // Validasi
    $('#error-bayar-sales').remove();
    if (bayar < total) {
        $('#btn-save-sales-payment').prop('disabled', true);
        $('#input-bayar-sales').after('<small id="error-bayar-sales" class="text-danger">Jumlah bayar tidak boleh kurang dari total yang harus dibayar.</small>');
    } else {
        $('#btn-save-sales-payment').prop('disabled', false);
    }
});

// ===================================================
// 3️⃣ SIMPAN PEMBAYARAN SALES
// ===================================================
$(document).on('submit', '#form-create-sales-payment', function(e) {
    e.preventDefault();

    const salesId = $('#payment_sales_id').val();
    const bayarClean = $('#input-bayar-sales').val().replace(/[^\d]/g, '');
    const kembaliClean = $('#kembalian-sales').val().replace(/[^\d]/g, '');

    // Tambahkan input hidden
    $('<input>').attr({type: 'hidden', name: 'amount_paid', value: bayarClean}).appendTo('#form-create-sales-payment');
    $('<input>').attr({type: 'hidden', name: 'change_amount', value: kembaliClean}).appendTo('#form-create-sales-payment');

    $.ajax({
        url: `/sales-payments/${salesId}`,
        type: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            if (res.status) {
                Swal.fire({title: 'Berhasil!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false});
                $('#btn-print-sales-payment').removeClass('d-none').off('click').on('click', function() {
                    window.open(`/sales/${salesId}/print`, '_blank');
                });
                if (typeof loadSales === 'function') loadSales();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
        }
    });
});

// ===================================================
// 4️⃣ RESET MODAL
// ===================================================
$('#createSalesPayment').on('hidden.bs.modal', function() {
    $('#form-create-sales-payment')[0].reset();
    $('#payment-detail-items').html('');
    $('#sisa-bayar-sales, #kembalian-sales').text('Rp 0');
    $('#btn-print-sales-payment').addClass('d-none');
    $('#error-bayar-sales').remove();
});
