   // Ubah Status
            $(document).on('click', '.btn-change-statusBayar', function() {
                let id = $(this).data('id');
                let status_bayar = $(this).data('status_bayar');

                $('#status_service_id').val(id);
                $('#new_status_bayar').val(status_bayar);
                $('#statusModalBayar').modal('show');
            });
            $(document).on('submit', '#form-status-service-bayar', function(e) {
                e.preventDefault();
                let id = $('#status_service_id').val();
                let status_bayar = $('#new_status_bayar').val();

                $.ajax({
                    url: "{{ url('services') }}/" + id + "/statusBayar",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status_bayar: status_bayar
                    },
                    success: function(res) {
                        $('#statusModalBayar').modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Status service berhasil diperbarui',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadServices(currentPage, searchQuery);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
