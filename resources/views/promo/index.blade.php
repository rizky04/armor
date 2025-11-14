@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Promo List</h4>
            <h6>Manage your promo</h6>
        </div>
        @can('promo-create')
            <div class="page-btn">
                <button class="btn btn-added" id="add-promo-btn">Tambah Promo</button>
            </div>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Promo</th>
                            <th>Beli (x)</th>
                            <th>Gratis (x)</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="promo-body">
                        <tr>
                            <td colspan="5" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- promo modal --}}
   <!-- Modal Tambah/Edit Promo -->
<div class="modal fade" id="promoModal" tabindex="-1" role="dialog" aria-labelledby="promoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="promo-form">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="promoModalLabel">Tambah Promo</h5>
            <button type="button" class="close" data-dismiss="modal" id="close-modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="promo-id">
              <div class="form-group">
                  <label for="promo-name">Nama Promo</label>
                  <input type="text" class="form-control" id="promo-name" required>
              </div>
              <div class="form-group">
                  <label for="buy-count">Buy Count</label>
                  <input type="number" class="form-control" id="buy-count" value="7" min="1" required>
              </div>
              <div class="form-group">
                  <label for="free-count">Free Count</label>
                  <input type="number" class="form-control" id="free-count" value="1" min="1" required>
              </div>
              <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="is-active" checked>
                  <label class="form-check-label" for="is-active">Aktif</label>
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
    {{-- promo modal --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                const promoUrl = "/promos";

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                function loadPromos() {
                    $.get(promoUrl, function(data) {
                        let rows = '';
                        if (data.length > 0) {
                            data.forEach(promo => {
                                rows += `
                            <tr>
                                <td>${promo.name}</td>
                                <td>${promo.buy_count}</td>
                                <td>${promo.free_count}</td>
                                <td>
                                    <span class="badge ${promo.is_active ? 'bg-success' : 'bg-secondary'}">
                                        ${promo.is_active ? 'Aktif' : 'Nonaktif'}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm  edit-promo" data-id="${promo.id}" data-name="${promo.name}" data-buy="${promo.buy_count}" data-free="${promo.free_count}" data-active="${promo.is_active}"><img src="{{ asset('assets/assets/img/icons/edit.svg') }}" alt="img"></button>
                                    <button class="btn btn-sm btn-warning toggle-promo" data-id="${promo.id}">
                                        ${promo.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                    </button>
                                    <button class="btn btn-sm  delete-promo" data-id="${promo.id}"><img src="{{ asset('assets/assets/img/icons/delete.svg') }}" alt="img"></button>
                                </td>
                            </tr>
                        `;
                            });
                        } else {
                            rows = `<tr><td colspan="5" class="text-center">Belum ada promo</td></tr>`;
                        }
                        $('#promo-body').html(rows);
                    });
                }

                loadPromos();

                // Tambah Promo
                $('#add-promo-btn').click(function() {
                    $('#promo-form')[0].reset();
                    $('#promo-id').val('');
                    $('#promoModalLabel').text('Tambah Promo');
                    $('#promoModal').modal('show');
                });

                $('#close-modal').click(function() {
                    $('#promoModal').modal('hide');
                });

                // Simpan / Update Promo
                $('#promo-form').submit(function(e) {
                    e.preventDefault();
                    let id = $('#promo-id').val();
                    let method = id ? 'PUT' : 'POST';
                    let url = id ? `${promoUrl}/${id}` : promoUrl;

                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            name: $('#promo-name').val(),
                            buy_count: $('#buy-count').val(),
                            free_count: $('#free-count').val(),
                            is_active: $('#is-active').is(':checked') ? 1 : 0
                        },
                        success: function(res) {
                            $('#promoModal').modal('hide');
                            Swal.fire('Sukses', res.message, 'success');
                            loadPromos();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan',
                                'error');
                        }
                    });
                });

                // Edit Promo
                $(document).on('click', '.edit-promo', function() {
                $('#promo-id').val($(this).data('id'));
                $('#promo-name').val($(this).data('name'));
                $('#buy-count').val($(this).data('buy'));
                $('#free-count').val($(this).data('free'));
                $('#is-active').prop('checked', $(this).data('active') == 1);

                $('#promoModalLabel').text('Edit Promo');
                $('#promoModal').modal('show');
                });

                // Toggle Aktif / Nonaktif
                $(document).on('click', '.toggle-promo', function() {
                    let id = $(this).data('id');
                    $.ajax({
                        url: `${promoUrl}/${id}/toggle`,
                        method: 'PATCH',
                        success: function(res) {
                            Swal.fire('Sukses', res.message, 'success');
                            loadPromos();
                        }
                    });
                });

                // Hapus Promo
                $(document).on('click', '.delete-promo', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Yakin hapus promo?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `${promoUrl}/${id}`,
                                method: 'DELETE',
                                success: function(res) {
                                    Swal.fire('Sukses', res.message, 'success');
                                    loadPromos();
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
