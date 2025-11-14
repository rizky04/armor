 // tambah
                $('#btn-add').on('click', function() {
                    $('#vehicleForm')[0].reset();
                    $('#vehicle_id').val('');
                    $('#id_client').val(null).trigger('change'); // reset select2
                    $('#photo-preview').attr('src', '').hide(); // reset foto preview
                    $('#vehicleModal .modal-title').text('Add Vehicle');
                    $('#vehicleModal').modal('show');
                });

                // simpan
                $('#vehicleForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#vehicle_id').val();
                    console.log('Vehicle ID:', id); // Debug: cek nilai id
                    let url = id ? `/vehicles/${id}` : "vehicles/store";

                    let formData = new FormData(this);
                    if (id) formData.append('_method', 'PUT');
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#vehicleModal').modal('hide');
                            loadVehicles(currentPage, searchQuery);
                            Swal.fire('Success', res.message, 'success');
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let msg = Object.values(errors).map(e => e[0]).join('<br>');
                                Swal.fire('Error', msg, 'error');
                            } else {
                                Swal.fire('Error', 'Terjadi kesalahan saat simpan data', 'error');
                            }
                        }
                    });
                });
