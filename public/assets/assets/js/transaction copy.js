$(function () {
    // Realtime validation (on input/blur)
    function validateField(fieldId, errorId, message) {
        let value = $(fieldId).val().trim();
        if (value === "") {
            $(errorId).text(message);
            return false;
        } else {
            $(errorId).text('');
            return true;
        }
    }

    $('#name').on('input blur', function () {
        validateField('#name', '#error-name', 'Nama wajib diisi.');
    });

    $('#no_telp').on('input blur', function () {
        validateField('#no_telp', '#error-no_telp', 'No. Telepon wajib diisi.');
    });

    $('#plate_number').on('input blur', function () {
        validateField('#plate_number', '#error-plate_number', 'Plat Nomor wajib diisi.');
    });

    // Submit form via AJAX
    $('#customerForm').on('submit', function (e) {
        e.preventDefault();

        let validName = validateField('#name', '#error-name', 'Nama wajib diisi.');
        let validPhone = validateField('#no_telp', '#error-no_telp', 'No. Telepon wajib diisi.');
        let validPlate = validateField('#plate_number', '#error-plate_number', 'Plat Nomor wajib diisi.');

        if (!validName || !validPhone || !validPlate) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon lengkapi semua field wajib.'
            });
            return;
        }

        $.ajax({
            url: customerStoreUrl, // <- ambil dari blade (lihat poin 2)
            method: "POST",
            data: $('#customerForm').serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Customer berhasil ditambahkan!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#create').modal('hide');
                    $('#customerForm')[0].reset();
                    $('.text-danger').text('');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.error
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#error-name').text(errors.name[0]);
                    }
                    if (errors.no_telp) {
                        $('#error-no_telp').text(errors.no_telp[0]);
                    }
                    if (errors.plate_number) {
                        $('#error-plate_number').text(errors.plate_number[0]);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi gagal',
                        text: 'Periksa kembali inputan Anda.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: xhr.responseJSON?.error || 'Terjadi kesalahan pada server.'
                    });
                }
            }
        });
    });
});


$(document).ready(function () {
    $('#customer_id').select2({
        placeholder: "Cari Customer...",
        allowClear: true,
        ajax: {
            url: customerSearchUrl, // route untuk search
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term }; // query search
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: item.name + ' - ' + item.plate_number + ' (' + item.squence + ')'
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
});


$(document).ready(function () {
    let totalItems = 0;

    function calculateTotal() {
        let total = 0;
    $('#product-list .product-lists').each(function () {
        const price = parseFloat($(this).find('.product-price').data('price'));
        const qty = parseInt($(this).find('.quantity-field').val());
        total += price * qty;
    });

    $('#total-price').text(total.toLocaleString());

    // ambil jenis diskon
    let discountType = $('#discount_type').val();
    let discountInput = parseFloat($('#discount').val()) || 0;
    let discountAmount = 0;

    if (discountType === 'percent') {
        discountAmount = total * (discountInput / 100);
    } else if (discountType === 'nominal') {
        discountAmount = discountInput;
    }

    let totalAfterDiscount = total - discountAmount;
    if (totalAfterDiscount < 0) totalAfterDiscount = 0;

    // hitung tax
    let taxPercent = parseFloat($('#tax').val()) || 0;
    let taxAmount = totalAfterDiscount * (taxPercent / 100);
    let totalAfterTax = totalAfterDiscount + taxAmount;

    $('#total-after-tax').text(totalAfterTax.toLocaleString());

    // hitung kembalian
    let cash = parseFloat($('#cash').val()) || 0;
    let kembalian = cash - totalAfterTax;
    $('#kembalian').text(kembalian >= 0 ? kembalian.toLocaleString() : 0);
    }

    // === semua event lama tetap ===
    $(document).on('click', '.product-item', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const image = $(this).data('image');

        if ($('#product-' + id).length) {
            let qtyInput = $('#product-' + id).find('.quantity-field');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            calculateTotal();
            return;
        }

        const html = `
            <li class="product-lists d-flex justify-content-between align-items-center" id="product-${id}" data-id="${id}">
                <div class="productimg d-flex align-items-center">
                    <div class="productimgs me-2">
                        <img src="${image}" alt="${name}" width="40">
                    </div>
                    <div class="productcontet">
                        <h4>${name}</h4>
                        <div class="increment-decrement">
                            <div class="input-groups">
                                <input type="button" value="-" class="button-minus dec button">
                                <input type="text" name="quantity[${id}]" value="1" class="quantity-field">
                                <input type="button" value="+" class="button-plus inc button ">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-price" data-price="${price}">${price.toLocaleString()}</div>
                <a class="confirm-text remove-product ms-2" data-id="${id}" href="javascript:void(0);">
                    <img src="${deleteIcon}" alt="hapus" width="20">
                </a>
            </li>
        `;

        $('#product-list').append(html);
        totalItems++;
        $('#total-items').text(totalItems);
        calculateTotal();
    });

    $(document).on('click', '.remove-product', function () {
        const id = $(this).data('id');
        $('#product-' + id).remove();
        totalItems--;
        $('#total-items').text(totalItems);
        calculateTotal();
    });

    $('#clear-all').on('click', function () {
        $('#product-list').empty();
        totalItems = 0;
        $('#total-items').text(totalItems);
        $('#total-price').text("0");
        $('#total-after-tax').text("0");
        $('#kembalian').text("0");
    });

    $(document).on('click', '.button-plus', function () {
        let $input = $(this).siblings('.quantity-field');
        $input.val(parseInt($input.val()) + 1);
        calculateTotal();
    });

    $(document).on('click', '.button-minus', function () {
        let $input = $(this).siblings('.quantity-field');
        let val = parseInt($input.val());
        if (val > 1) $input.val(val - 1);
        calculateTotal();
    });

    $(document).on('input', '.quantity-field', function () {
        let val = parseInt($(this).val());
        if (isNaN(val) || val < 1) $(this).val(1);
        calculateTotal();
    });

    // hitung ulang jika tax atau cash diubah
    // $('#tax, #cash').on('input', function () {
    //     calculateTotal();
    // });
    $('#discount_type, #discount, #tax, #cash').on('input change', function () {
        calculateTotal();
    });
    console.log({
        total,
        discountType,
        discountInput,
        discountAmount,
        totalAfterDiscount,
        taxPercent,
        taxAmount,
        totalAfterTax,
        cash,
        kembalian
    });
});

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#save-transaction').on('click', function () {
        let items = [];

        $('#product-list .product-lists').each(function () {
            let productId = $(this).data('id');
            let price = parseFloat($(this).find('.product-price').data('price'));
            let qty = parseInt($(this).find('.quantity-field').val());
            let subtotal = price * qty;

            items.push({
                product_id: productId,
                price: price,
                qty: qty,
                subtotal: subtotal
            });
        });

        let data = {
            customer_id: $('#customer_id').val(),
            total: parseFloat($('#total-price').text().replace(/,/g, '')),
            discount: parseFloat($('#discount').val()),
            tax: parseFloat($('#tax').val()),
            total_after_tax: parseFloat($('#total-after-tax').text().replace(/,/g, '')),
            cash: parseFloat($('#cash').val()),
            change: parseFloat($('#kembalian').text().replace(/,/g, '')),
            items: items,
            // _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: transactionUrl,
            method: "POST",
            data: data,
            success: function (response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#product-list').empty();
                    $('#total-items').text(0);
                    $('#total-price').text(0);
                    $('#total-after-tax').text(0);
                    $('#cash').val(0);
                    $('#kembalian').text(0);
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });
});

$(document).ready(function () {
    // function loadTransactions(search = '') {
    //     $.ajax({
    //         url: transactionDetailUrl, // endpoint untuk ambil data transaksi
    //         method: "GET",
    //         data: { search: search },
    //         success: function (response) {
    //             let rows = '';
    //             if (response.data.length > 0) {
    //                 response.data.forEach(trx => {
    //                     let dateObj = new Date(trx.created_at);

    // // Format YYYY-MM-DD
    // let formattedDate = dateObj.toISOString().split('T')[0];
    //                     rows += `
    //                         <tr>
    //                             <td>${formattedDate}</td>
    //                             <td>${trx.reference}</td>
    //                             <td>${trx.customer ? trx.customer.name : '-'}</td>
    //                             <td>${parseFloat(trx.total_after_tax).toLocaleString()}</td>
    //                             <td class="text-end">
    //     <button type="button" class="btn btn-sm btn-light delete-transaction" data-id="${trx.id}">
    //         <img src="/assets/assets/img/icons/delete.svg" alt="hapus">
    //     </button>
    //                             </td>
    //                         </tr>
    //                     `;
    //                 });
    //             } else {
    //                 rows = `<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>`;
    //             }
    //             $('#transaction-body').html(rows);
    //         },
    //         error: function () {
    //             Swal.fire('Error', 'Gagal memuat data transaksi', 'error');
    //         }
    //     });
    // }

    // // Panggil saat pertama kali load halaman
    // loadTransactions();

    // $('#search-transaction').on('input', function () {
    //     let search = $(this).val();
    //     loadTransactions(search);
    // });

    // // 🔹 Refresh manual
    // $('#refresh-transaction').on('click', function () {
    //     $('#search-transaction').val(''); // reset pencarian
    //     loadTransactions();
    // });

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
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light delete-transaction" data-id="${trx.id}">
                                    <img src="/assets/assets/img/icons/delete.svg" alt="hapus">
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
