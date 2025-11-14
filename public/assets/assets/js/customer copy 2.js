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

 let promoFree = false;

$(document).ready(function () {

    // Inisialisasi Select2 untuk pencarian customer
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
                            text: item.name + ' - ' + item.plate_number + ' (Cuci ' + item.squence + ' X)' + ' (Free Voucher ' + item.free_voucher + ')'
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    // Cek promo ketika customer dipilih
    $('#customer_id').on('change', function () {
    let customerId = $(this).val();
    console.log("Selected customer ID:", customerId);
    if (!customerId) return;


    $.get(`/customers/${customerId}/promo-check`, function (res) {
        if (res.eligible_free) {
            promoFree = true;
            Swal.fire('Info Promo', res.message, 'success');
            // Kalau mau otomatis set total jadi 0 sebelum bayar:
            // $('#total-price').text('0');
            // $('#total-after-tax').text('0');
             calculateTotal();
        } else {
            promoFree = false;
            Swal.fire('Info Customer', res.message, 'info');
            calculateTotal();
        }
    });
});
});

