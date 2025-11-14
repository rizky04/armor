  $(document).on('click', '.btn-delete', function() {
                let url = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data service akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                // _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: res.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // sekarang aman, fungsi global
                                loadServices(currentPage, searchQuery);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
