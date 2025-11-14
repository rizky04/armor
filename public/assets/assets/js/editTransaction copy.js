$(document).ready(function () {
    let currentPage = 1;
    let perPage = 5;

    // Fungsi load transaksi
    function loadTransactions(search = '', page = 1, startDate = '', endDate = '') {
        $.ajax({
            url: transactionDetailUrl,
            method: "GET",
            data: {
                search: search,
                page: page,
                per_page: perPage,
                start_date: startDate,
                end_date: endDate
            },
            success: function (response) {
                let rows = '';

                // Isi tabel
                if (response.data.length > 0) {
                    response.data.forEach(trx => {
                        let formattedDate = trx.created_at
                            ? new Date(trx.created_at).toISOString().split('T')[0]
                            : '';

                        rows += `
                        <tr>
                            <td>${formattedDate}</td>
                            <td>${trx.reference}</td>
                            <td>${trx.customer ? trx.customer.name : 'Walk-in Customer'}</td>
                            <td>Rp ${parseFloat(trx.total_after_tax).toLocaleString()}</td>
                            <td>
                            ${trx.plate_photo
                                ? `<img src="/uploads/plates/${trx.plate_photo}" width="70">`
                                : '-'}
                        </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light delete-transaction" data-id="${trx.id}">
                                    <img src="/assets/assets/img/icons/delete.svg" alt="hapus">
                                </button>
                                <button type="button" class="btn btn-sm btn-light btn-edit-transaction" data-id="${trx.id}">
                                    <img src="/assets/assets/img/icons/edit.svg" alt="edit">
                                </button>
                                <button type="button" class="btn btn-sm btn-light btn-print-transaction" data-id="${trx.id}">
            <img src="/assets/assets/img/icons/printer.svg" alt="print">
        </button>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`;

                    // Info jika ada filter atau pencarian
                    if (search || startDate || endDate) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Data tidak ditemukan',
                            text: 'Tidak ada transaksi yang sesuai dengan filter/pencarian Anda',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }

                $('#transaction-body').html(rows);

                // Buat pagination
                let pagination = '';
                if (response.meta && response.meta.last_page > 1) {
                    for (let i = 1; i <= response.meta.last_page; i++) {
                        pagination += `
                        <li class="page-item ${i === response.meta.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                    }
                }
                $('#pagination').html(pagination);
            },
            error: function () {
                Swal.fire('Error', 'Gagal memuat data transaksi', 'error');
            }
        });
    }

    // Load pertama kali
    loadTransactions();

    // Search realtime
    $('#search-transaction').on('input', function () {
        currentPage = 1;
        loadTransactions($(this).val(), currentPage, $('#start-date').val(), $('#end-date').val());
    });

    // Filter range tanggal
    $('#filter-transaction').on('click', function () {
        currentPage = 1;
        loadTransactions($('#search-transaction').val(), currentPage, $('#start-date').val(), $('#end-date').val());
    });

    // Refresh
    $('#refresh-transaction').on('click', function () {
        $('#search-transaction').val('');
        $('#start-date').val('');
        $('#end-date').val('');
        currentPage = 1;
        loadTransactions();
    });

    // Event pagination
    $(document).on('click', '#pagination .page-link', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadTransactions($('#search-transaction').val(), currentPage, $('#start-date').val(), $('#end-date').val());
        }
    });
    $(document).on('click', '.delete-transaction', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let id = $(this).data('id');
        console.log("Klik hapus transaksi ID:", id);
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Transaksi akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: transactionDeleteUrl.replace(':id', id),
                    method: "DELETE",
                    // data: {
                    //     _token: "{{ csrf_token() }}"
                    // },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("Berhasil", response.message, "success");
                            loadTransactions(); // reload tabel
                        } else {
                            Swal.fire("Gagal", response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire("Error", "Gagal menghapus transaksi", "error");
                    }
                });
            }
        });
    });
});

$(document).on('click', '.btn-edit-transaction', function () {
    let id = $(this).data('id');

    $.get(`/transactions/${id}`, function (res) {
        if (res.success) {
            let trx = res.data;

            $('#edit-transaction-id').val(trx.id);
            $('#edit-customer-id').val(trx.customer.name);
            $('#edit-discount').val(trx.discount);
            $('#edit-tax').val(trx.tax);
            $('#edit-cash').val(trx.cash);

            let rows = '';
            trx.items.forEach(item => {
                rows += `
                    <tr class="edit-item-row" data-id="${item.product_id}">
                        <td>${item.product.name}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm edit-qty" value="${item.qty}" min="1">
                        </td>
                        <td>
                            Rp <span class="edit-price" data-price="${item.price}">${item.price.toLocaleString()}</span>
                        </td>
                        <td>
                            Rp <span class="edit-subtotal">${(item.qty * item.price).toLocaleString()}</span>
                        </td>
                    </tr>
                `;
            });
            $('#edit-items').html(rows);

            EditCalculateTotals();

            $('#editTransactionModal').modal('show');
        }
    });
});

$(document).on('click', '.btn-print-transaction', function () {
    let id = $(this).data('id');
    window.open(transactionPrintUrl.replace(':id', id), '_blank');
});


$('#edit-transaction-form').submit(function (e) {
    e.preventDefault();

    let formData = new FormData();

    // data transaksi
    formData.append('customer_id', $('#edit-customer').val());
    formData.append('discount', $('#edit-discount').val());
    formData.append('tax', $('#edit-tax').val());
    formData.append('cash', $('#edit-cash').val());
    formData.append('total', $('#edit-total-price').text().replace(/,/g, ''));
    formData.append('total_after_tax', $('#edit-total-after-tax').text().replace(/,/g, ''));
    formData.append('change', $('#edit-kembalian').text().replace(/,/g, ''));

    // items
    $('#edit-items .edit-item-row').each(function (i) {
        formData.append(`items[${i}][product_id]`, $(this).data('id'));
        formData.append(`items[${i}][qty]`, $(this).find('.edit-qty').val());
        formData.append(`items[${i}][price]`, $(this).find('.edit-price').data('price'));
        formData.append(`items[${i}][subtotal]`, $(this).find('.edit-qty').val() * $(this).find('.edit-price').data('price'));
    });

    // file (kalau ada)
    let file = $('#edit-image')[0].files[0];
    if (file) {
        formData.append('image', file);
    }

    let id = $('#edit-transaction-id').val();

    $.ajax({
        url: `/transactions/${id}`,
        method: 'POST', // pakai POST + _method=PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            $('#editTransactionModal').modal('hide');
            Swal.fire('Sukses', res.message, 'success');
            loadTransactions();
        },
        error: function (xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
        }
    });
});

function EditCalculateTotals() {
    let subtotal = 0;

    // loop semua item dalam modal edit
    $('#edit-items .edit-item-row').each(function () {
        let qty = parseInt($(this).find('.edit-qty').val()) || 0;
        let price = parseFloat($(this).find('.edit-price').data('price')) || 0;
        let subtotalItem = qty * price;

        $(this).find('.edit-subtotal').text(subtotalItem.toLocaleString()); // tampilkan subtotal per item

        subtotal += subtotalItem;
    });

    let discount = parseFloat($('#edit-discount').val()) || 0;
    let tax = parseFloat($('#edit-tax').val()) || 0;

    let afterDiscount = subtotal - discount;
    let totalAfterTax = afterDiscount + tax;

    $('#edit-total-price').text(subtotal.toLocaleString());
    $('#edit-total-after-tax').text(totalAfterTax.toLocaleString());

    let cash = parseFloat($('#edit-cash').val()) || 0;
    let change = cash - totalAfterTax;

    $('#edit-kembalian').text(change.toLocaleString());
}

// jalankan saat qty, discount, tax, cash berubah
$(document).on('input', '.edit-qty, #edit-discount, #edit-tax, #edit-cash', function () {
    EditCalculateTotals();
});

$(document).on('click', '.btn-edit-transaction', function () {
    let id = $(this).data('id');

    $.get(`/transactions/${id}`, function (res) {
        if (res.success) {
            let trx = res.data;

            $('#edit-transaction-id').val(trx.id);
            $('#edit-customer').val(trx.customer_id);
            $('#edit-discount').val(trx.discount);
            $('#edit-tax').val(trx.tax);
            $('#edit-cash').val(trx.cash);

            let rows = '';
            trx.items.forEach(item => {
                rows += `
                    <tr class="edit-item-row" data-id="${item.product_id}">
                        <td>${item.product.name}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm edit-qty" value="${item.qty}" min="1">
                        </td>
                        <td>
                            Rp <span class="edit-price" data-price="${item.price}">${item.price.toLocaleString()}</span>
                        </td>
                        <td>
                            Rp <span class="edit-subtotal">${(item.qty * item.price).toLocaleString()}</span>
                        </td>
                    </tr>
                `;
            });
            $('#edit-items').html(rows);

            EditCalculateTotals();

            $('#editTransactionModal').modal('show');
        }
    });
});
