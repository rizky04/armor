  // Ubah Status
            $(document).on('click', '.btn-change-status', function() {
                let id = $(this).data('id');
                let status = $(this).data('status');

                $('#status_service_id').val(id);
                $('#new_status').val(status);
                $('#statusModal').modal('show');
            });
            $(document).on('submit', '#form-status-service', function(e) {
                e.preventDefault();
                let id = $('#status_service_id').val();
                let status = $('#new_status').val();

                $.ajax({
                    // url: "{{ url('services') }}/" + id + "/status",
                    url: `update/services/${id}/status`,
                    type: "POST",
                    data: {
                        // _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(res) {
                        $('#statusModal').modal('hide');
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
