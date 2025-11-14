 // hapus
                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/vehicles/${id}`,
                                method: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    loadVehicles(currentPage, searchQuery);
                                    Swal.fire('Deleted!', res.message, 'success');
                                }
                            });
                        }
                    });
                });
