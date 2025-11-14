  $('#add-client-btn').on('click', function() {
                    $('#clientForm')[0].reset();
                    $('#id_client').val('');
                    $('#clientModal .modal-title').text('Tambah Client');
                    $('#clientModal').modal('show');
                });

                $('#clientForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#id_client').val();
                    let url = id ? `/client/${id}` : "{{ route('client.store') }}";
                    let method = id ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            nama_client: $('#nama_client').val(),
                            no_telp: $('#no_telp').val(),
                            no_ktp: $('#no_ktp').val(),
                            alamat: $('#alamat').val(),
                            hapus: $('#hapus').val(),
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            $('#clientModal').modal('hide');
                            Swal.fire('Success', res.message, 'success');
                        }
                    });
                });
