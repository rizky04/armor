  // ===================================================
            // 1️⃣ TAMPILKAN MODAL PEMBAYARAN
            // ===================================================
            $(document).on('click', '.btn-create-payment', function() {
                let id = $(this).data('id');
                let nomorService = $(this).data('nomor');

                // Reset form dan modal
                $('#form-create-payment')[0].reset();
                $('#payment_service_id').val(id);
                $('#reference').val(nomorService || '-');
                $('#payment-detail-jasa, #payment-detail-sparepart').html('');
                $('#sisa-bayar').text('Rp 0');
                $('#input-bayar').val('');
                $('#kembalian').val('Rp 0');
                $('#btn-print-payment').addClass('d-none');
                $('#btn-save-payment').removeClass('d-none').prop('disabled', false);

                // Hapus pesan error jika ada
                $('#error-bayar').remove();

                $('#createpayment').modal('show');

                // Ambil data service
                $.ajax({
                    url: `/services/${id}/payment-detail`,
                    type: 'GET',
                    success: function(res) {
                        if (res.status) {
                            const data = res.data;
                            console.log("tes", data);
                            // ==== Isi Tabel Jasa ====
                            let jasaHtml = '';
                            data.service.jobs.forEach(job => {
                                jasaHtml += `
                        <tr>
                            <td>${job.jasa?.nama_jasa ?? '-'}</td>
                            <td class="text-center">${job.qty}</td>
                            <td class="text-end">Rp ${Number(job.price).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${Number(job.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                            });
                            $('#payment-detail-jasa').html(jasaHtml);

                            // ==== Isi Tabel Sparepart ====
                            let spHtml = '';
                            data.service.spareparts.forEach(sp => {
                                spHtml += `
                        <tr>
                            <td>${sp.barang?.nama_barang ?? '-'}</td>
                            <td class="text-center">${sp.qty}</td>
                            <td class="text-end">Rp ${Number(sp.price).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${Number(sp.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                            });
                            $('#payment-detail-sparepart').html(spHtml);

                            // ==== Total & Sisa Bayar ====
                            const sisa = Number(data.sisa_bayar);
                            $('#sisa-bayar').text(`Rp ${sisa.toLocaleString('id-ID')}`);

                            // ==== Otomatis isi nominal bayar ====
                            $('#input-bayar').val(sisa.toLocaleString('id-ID'));

                            // ==== Hitung ulang kembalian ====
                            $('#kembalian').val('Rp 0');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memuat data service.', 'error');
                    }
                });
            });

            // ===================================================
            // 2️⃣ FORMAT INPUT DAN HITUNG KEMBALIAN + VALIDASI
            // ===================================================
            $(document).on('input', '#input-bayar', function() {
                // Format otomatis dengan titik ribuan
                let value = $(this).val().replace(/[^\d]/g, '');
                if (value) {
                    $(this).val(Number(value).toLocaleString('id-ID'));
                } else {
                    $(this).val('');
                }

                // Hitung kembalian
                const total = parseFloat($('#sisa-bayar').text().replace(/[^\d]/g, '')) || 0;
                const bayar = parseFloat(value) || 0;
                const kembali = bayar - total;

                $('#kembalian').val(kembali > 0 ? `Rp ${kembali.toLocaleString('id-ID')}` : 'Rp 0');

                // Validasi pembayaran
                $('#error-bayar').remove();
                if (bayar < total) {
                    $('#btn-save-payment').prop('disabled', true);
                    $('#input-bayar').after(
                        '<small id="error-bayar" class="text-danger">Jumlah bayar tidak boleh kurang dari total yang harus dibayar.</small>'
                    );
                } else {
                    $('#btn-save-payment').prop('disabled', false);
                }
            });

            // ===================================================
            // 3️⃣ SIMPAN PEMBAYARAN
            // ===================================================
            $(document).on('submit', '#form-create-payment', function(e) {
                e.preventDefault();

                const serviceId = $('#payment_service_id').val();



                // Bersihkan titik ribuan sebelum kirim ke backend
                const bayarClean = $('#input-bayar').val().replace(/[^\d]/g, '');
                const kembaliClean = $('#kembalian').val().replace(/[^\d]/g, '');
                // Hapus input lama biar tidak dobel
                $('input[name="amount_paid"]').remove();
                $('input[name="change_amount"]').remove();


                $('<input>').attr({
                    type: 'hidden',
                    name: 'amount_paid',
                    value: bayarClean
                }).appendTo('#form-create-payment');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'change_amount',
                    value: kembaliClean
                }).appendTo('#form-create-payment');

                $.ajax({
                    url: `/service-payments/${serviceId}`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#btn-save-payment').addClass('d-none');
                            $('#btn-print-payment').removeClass('d-none')
                                .off('click')
                                .on('click', function() {
                                    window.open(`/services/${serviceId}/print`, '_blank');
                                });



                            if (typeof loadServices === 'function') loadServices();
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
            $('#createpayment').on('hidden.bs.modal', function() {
                $('#form-create-payment')[0].reset();
                $('#payment-detail-jasa, #payment-detail-sparepart').html('');
                $('#sisa-bayar, #kembalian').text('Rp 0');
                $('#btn-save-payment').show().prop('disabled', false);
                $('#btn-print-payment').hide();
                $('#error-bayar').remove();
            });
