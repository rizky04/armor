 $('#id_client').select2({
                    dropdownParent: $('#vehicleModal'),
                    placeholder: 'Pilih Client...',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('select2.clients') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id_client,
                                        text: item.nama_client + ' - ' + (item.alamat ?? '')
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1
                });
