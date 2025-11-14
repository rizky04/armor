 // edit
                $(document).on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    $.get(`/vehicles/${id}`, function(v) {
                        $('#vehicle_id').val(v.id);
                        $('#license_plate').val(v.license_plate);
                        $('#brand').val(v.brand);
                        $('#type').val(v.type);
                        $('#engine_number').val(v.engine_number);
                        $('#chassis_number').val(v.chassis_number);

                        // set customer di select2
                        if (v.client) {
                            let option = new Option(v.client.nama_client, v.id_client, true, true);
                            $('#id_client').append(option).trigger('change');
                        }

                        // tampilkan preview foto
                        if (v.photo) {
                            $('#photo-preview').attr('src', '/uploads/vehicle/' + v.photo).show();
                        } else {
                            $('#photo-preview').attr('src', '').hide();
                        }

                        $('#vehicleModal .modal-title').text('Edit Vehicle');
                        $('#vehicleModal').modal('show');
                    });
                });
