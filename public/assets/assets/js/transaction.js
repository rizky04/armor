function formatRupiah(angka) {
    return "Rp" + Number(angka).toLocaleString("id-ID");
}
// function calculateTotal() {
//     if (promoFree) {
//         $('#total-price').text("Rp0");
//         $('#total-after-tax').text("Rp0");
//         let cash = parseFloat($('#cash').val()) || 0;
//         let kembalian = cash; // semua cash kembali karena total 0
//         $('#kembalian').text(formatRupiah(kembalian));
//         return; // hentikan, jangan lanjut hitung normal
//     }
//     let total = 0;
//     $('#product-list .product-lists').each(function () {
//         const price = parseFloat($(this).find('.product-price').data('price'));
//         const qty = parseInt($(this).find('.quantity-field').val());
//         total += price * qty;
//     });


//     $('#total-price').text(formatRupiah(total));

//     // ambil jenis diskon
//     let discountType = $('#discount_type').val();
//     let discountInput = parseFloat($('#discount').val()) || 0;
//     let discountAmount = 0;

//     if (discountType === 'percent') {
//         discountAmount = total * (discountInput / 100);
//     } else if (discountType === 'nominal') {
//         discountAmount = discountInput;
//     }

//     let totalAfterDiscount = total - discountAmount;
//     if (totalAfterDiscount < 0) totalAfterDiscount = 0;

//     // hitung tax
//     let taxPercent = parseFloat($('#tax').val()) || 0;
//     let taxAmount = totalAfterDiscount * (taxPercent / 100);
//     let totalAfterTax = totalAfterDiscount + taxAmount;

//     $('#total-after-tax').text(formatRupiah(totalAfterTax));

//     // hitung kembalian
//     let cash = parseFloat($('#cash').val()) || 0;
//     let kembalian = cash - totalAfterTax;
//     $('#kembalian').text(kembalian >= 0 ? formatRupiah(kembalian) : 0);
// }

// function calculateTotal() {
//     let total = 0;
//     let hasCuci = false;
//     let hasNonCuci = false;

//     $('#product-list .product-lists').each(function () {
//         const price = parseFloat($(this).find('.product-price').data('price'));
//         const qty = parseInt($(this).find('.quantity-field').val());
//         const category = $(this).data('category'); // ambil kategori

//         if (promoFree && category === "cuci") {
//             hasCuci = true;
//             // kalau promo free → cuci gratis
//             $(this).find('.product-price').text("Rp0");
//         } else {
//             if (category === "cuci") hasCuci = true;
//             if (category !== "cuci") hasNonCuci = true;

//             let subtotal = price * qty;
//             total += subtotal;
//             $(this).find('.product-price').text(formatRupiah(subtotal));
//         }
//     });

//     // --- kalau promo free & semua item cuci → total = 0 full ---
//     if (promoFree && hasCuci && !hasNonCuci) {
//         $('#total-price').text("Rp0");
//         $('#total-after-tax').text("Rp0");
//         let cash = parseFloat($('#cash').val()) || 0;
//         $('#kembalian').text(formatRupiah(cash));
//         return;
//     }

//     // tampilkan total sementara
//     $('#total-price').text(formatRupiah(total));

//     // ambil jenis diskon
//     let discountType = $('#discount_type').val();
//     let discountInput = parseFloat($('#discount').val()) || 0;
//     let discountAmount = 0;

//     if (discountType === 'percent') {
//         discountAmount = total * (discountInput / 100);
//     } else if (discountType === 'nominal') {
//         discountAmount = discountInput;
//     }

//     let totalAfterDiscount = total - discountAmount;
//     if (totalAfterDiscount < 0) totalAfterDiscount = 0;

//     // hitung tax persen
//     let taxPercent = parseFloat($('#tax').val()) || 0;
//     let taxAmount = totalAfterDiscount * (taxPercent / 100);
//     let totalAfterTax = totalAfterDiscount + taxAmount;

//     $('#total-after-tax').text(formatRupiah(totalAfterTax));

//     // hitung kembalian
//     let cash = parseFloat($('#cash').val()) || 0;
//     let kembalian = cash - totalAfterTax;
//     $('#kembalian').text(kembalian >= 0 ? formatRupiah(kembalian) : 0);
// }

// let cashEdited = false; // flag apakah user edit manual

// // kalau user ketik sendiri, tandai sebagai manual
// $('#cash').on('input', function() {
//     cashEdited = true;
//     calculateTotal(); // tetap hitung ulang biar kembalian update
// });
// function calculateTotal() {
//     let total = 0;
//     let hasCuci = false;
//     let hasNonCuci = false;

//     $('#product-list .product-lists').each(function () {
//         const price = parseFloat($(this).find('.product-price').data('price'));
//         const qty = parseInt($(this).find('.quantity-field').val());
//         const category = $(this).data('category');

//         if (promoFree && category === "cuci") {
//             hasCuci = true;
//             $(this).find('.product-price').text("Rp0");
//         } else {
//             if (category === "cuci") hasCuci = true;
//             if (category !== "cuci") hasNonCuci = true;

//             let subtotal = price * qty;
//             total += subtotal;
//             $(this).find('.product-price').text(formatRupiah(subtotal));
//         }
//     });

//     if (promoFree && hasCuci && !hasNonCuci) {
//         $('#total-price').text("Rp0");
//         $('#total-after-tax').text("Rp0");

//         // kalau belum diedit manual, isi otomatis Rp0
//         if (!cashEdited) {
//             $('#cash').val(0);
//         }

//         let cash = parseFloat($('#cash').val()) || 0;
//         $('#kembalian').text(formatRupiah(cash));
//         return;
//     }

//     $('#total-price').text(formatRupiah(total));

//     let discountType = $('#discount_type').val();
//     let discountInput = parseFloat($('#discount').val()) || 0;
//     let discountAmount = 0;

//     if (discountType === 'percent') {
//         discountAmount = total * (discountInput / 100);
//     } else if (discountType === 'nominal') {
//         discountAmount = discountInput;
//     }

//     let totalAfterDiscount = total - discountAmount;
//     if (totalAfterDiscount < 0) totalAfterDiscount = 0;

//     let taxPercent = parseFloat($('#tax').val()) || 0;
//     let taxAmount = totalAfterDiscount * (taxPercent / 100);
//     let totalAfterTax = totalAfterDiscount + taxAmount;

//     $('#total-after-tax').text(formatRupiah(totalAfterTax));

//     // kalau user belum edit manual → isi otomatis
//     if (!cashEdited) {
//         $('#cash').val(totalAfterTax);
//     }

//     let cash = parseFloat($('#cash').val()) || 0;
//     let kembalian = cash - totalAfterTax;
//     $('#kembalian').text(kembalian >= 0 ? formatRupiah(kembalian) : 0);
// }


let cashEdited = false; // flag apakah user edit manual

// Helper format Rupiah
function formatRupiah(angka, prefix = "Rp") {
    let number_string = angka.toString().replace(/[^,\d]/g, ""),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
    return prefix + rupiah;
}

// Ambil angka asli dari input Rp
function parseRupiah(str) {
    return parseFloat(str.replace(/[^0-9]/g, "")) || 0;
}

// Kalau user ketik cash → tandai manual + ubah ke format Rp
$('#cash').on('input', function () {
    cashEdited = true;

    let value = $(this).val();
    let number = parseRupiah(value);

    // Update tampilan dengan format Rp
    $(this).val(formatRupiah(number));

    calculateTotal();
});

function calculateTotal() {

    let total = 0;
    let hasCuci = false;
    let hasNonCuci = false;

    $('#product-list .product-lists').each(function () {
        const price = parseFloat($(this).find('.product-price').data('price'));
        const qty = parseInt($(this).find('.quantity-field').val());
        const category = $(this).data('category');

        if (promoFree && category === "cuci") {
            hasCuci = true;
            $(this).find('.product-price').text("Rp0");
        } else {
            if (category === "cuci") hasCuci = true;
            if (category !== "cuci") hasNonCuci = true;

            let subtotal = price * qty;
            total += subtotal;
            $(this).find('.product-price').text(formatRupiah(subtotal));
        }
    });

    if (promoFree && hasCuci && !hasNonCuci) {
        $('#total-price').text("Rp0");
        $('#total-after-tax').text("Rp0");

        if (!cashEdited) {
            $('#cash').val(formatRupiah(0));
        }

        let cash = parseRupiah($('#cash').val());
        $('#kembalian').text(formatRupiah(cash));
        return;
    }

    $('#total-price').text(formatRupiah(total));

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

    let taxPercent = parseFloat($('#tax').val()) || 0;
    let taxAmount = totalAfterDiscount * (taxPercent / 100);
    let totalAfterTax = totalAfterDiscount + taxAmount;

    $('#total-after-tax').text(formatRupiah(totalAfterTax));

    if (!cashEdited) {
        $('#cash').val(formatRupiah(totalAfterTax));
    }

    let cash = parseRupiah($('#cash').val());
    let kembalian = cash - totalAfterTax;
    $('#kembalian').text(kembalian >= 0 ? formatRupiah(kembalian) : 0);
}



$(document).ready(function () {
    let totalItems = 0;


    // === semua event lama tetap ===
    $(document).on('click', '.product-item', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const image = $(this).data('image');
        const category = $(this).data('category');

        if ($('#product-' + id).length) {
            let qtyInput = $('#product-' + id).find('.quantity-field');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            calculateTotal();
            return;
        }

        const html = `
            <li class="product-lists d-flex justify-content-between align-items-center" id="product-${id}" data-id="${id}" data-category="${category}">
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
                <div class="product-price" data-price="${price}">Rp ${price.toLocaleString()}</div>
                <a class="confirm-text remove-product ms-2" data-id="${id}" href="javascript:void(0);">
                    <img src="${deleteIcon}" alt="hapus" width="20">
                </a>
            </li>
        `;

        $('#product-list').append(html);
        updateTotalItems();
        calculateTotal();
    });

    function updateTotalItems() {
        totalItems = $('#product-list .product-lists').length;
        $('#total-items').text(totalItems);
    }

    $(document).on('click', '.remove-product', function () {
        const id = $(this).data('id');
        $('#product-' + id).remove();
        updateTotalItems();
        calculateTotal();
    });

    $('#clear-all').on('click', function () {
        $('#product-list').empty();
        updateTotalItems();
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

    $('#discount_type, #discount, #tax, #cash').on('input change', function () {
        calculateTotal();
    });
    // console.log({
    //     total,
    //     discountType,
    //     discountInput,
    //     discountAmount,
    //     totalAfterDiscount,
    //     taxPercent,
    //     taxAmount,
    //     totalAfterTax,
    //     cash,
    //     kembalian
    // });
});
$(document).on('change', 'input[name="payment_method"]', function() {
    let method = $(this).val();
    console.log("Metode dipilih:", method);
    // contoh: kirim ke form hidden
    $('#payment_method_hidden').val(method);
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

        let formData = new FormData();
        formData.append('customer_id', $('#customer_id').val());
        // formData.append('total', parseFloat($('#total-price').text().replace(/,/g, '')));
        formData.append('total', parseFloat($('#total-price').text().replace(/[^0-9]/g, '')));
        formData.append('discount', parseFloat($('#discount').val()));
        formData.append('tax', parseFloat($('#tax').val()));
        // formData.append('total_after_tax', parseFloat($('#total-after-tax').text().replace(/,/g, '')));
        formData.append('total_after_tax', parseFloat($('#total-after-tax').text().replace(/[^0-9]/g, '')));
        // formData.append('cash', parseFloat($('#cash').val()));
        formData.append('cash', parseFloat($('#cash').val().replace(/[^0-9]/g, '')));
        formData.append('payment_method', $('#payment_method_hidden').val());
        formData.append('change', parseFloat($('#kembalian').text().replace(/[^0-9]/g, '')));
        // formData.append('change', parseFloat($('#kembalian').text().replace(/,/g, '')));
        formData.append('items', JSON.stringify(items));

        // tambahkan foto plat nomor
        let photo = $('#plate_photo')[0].files[0];
        if (photo) {
            formData.append('plate_photo', photo);
        }

        console.log(formData),
        $.ajax({
            url: transactionUrl,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#product-list').empty();
                    $('#total-items').text(0);
                    $('#customer_id').val(null).trigger('change');
                    $('#total-price').text(0);
                    $('#discount_type').val('nominal');
                    $('#discount').val(0);
                    $('#tax').val(0);
                    $('#total-after-tax').text(0);
                    $('#cash').val(0);
                    $('#kembalian').text(0);
                    $('#plate_photo').val('');
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

