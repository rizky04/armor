@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Kartu pergantian Oli</h4>
            <h6>Pencatatan pergantian oli</h6>
        </div>
      <div class="page-btn">
                        <button class="btn btn-primary mb-3" id="btnAdd">+ Tambah Kartu Ganti Oli</button>

            </div>
    </div>


  <div class="card">
    <div class="card-body">

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Service</th>
                <th>Kendaraan</th>
                <th>Oli Digunakan</th>
                <th>KM Service</th>
                <th>KM Berikutnya</th>
                <th>Tanggal Berikutnya</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="oilTableBody">
            @foreach($oilServices as $item)
            <tr id="row-{{ $item->id }}">
              <td>{{ $loop->iteration }}</td>
                <td>{{ $item->service_date }}</td>
                <td>{{ $item->service->vehicle->nopol ?? '-' }}</td>
                <td>{{ $item->oil_name }}</td>
                <td>{{ $item->km_service }}</td>
                <td>{{ $item->km_service_next }}</td>
                <td>{{ $item->next_service_date }}</td>
                <td>
                    <button class="btn btn-warning btn-sm btnEdit" data-id="{{ $item->id }}">Edit</button>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="{{ $item->id }}">Hapus</button>
                 <a href="{{ route('oil_services.print', $item->id) }}" target="_blank" class="btn btn-info btn-sm">🖨️ Cetak</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $oilServices->links() }}
    </div>
  </div>


<!-- Modal Create/Edit -->
<div class="modal fade" id="oilModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="oilForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Tambah Ganti Oli</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="oil_id" name="id">

          <div class="form-group mb-2" id="serviceSelectGroup">
              <label for="service_id">Service (mengandung oli)</label>
              <select id="service_id" name="service_id" class="form-control"></select>
          </div>

          <div class="form-group mb-2">
              <label>Nama Oli</label>
              <input type="text" name="oil_name" id="oil_name" class="form-control">
          </div>
          <div class="form-group mb-2">
              <label>KM Service</label>
              <input type="number" name="km_service" id="km_service" class="form-control">
          </div>
          <div class="form-group mb-2">
              <label>KM Berikutnya</label>
              <input type="number" name="km_service_next" id="km_service_next" class="form-control">
          </div>
          <div class="form-group mb-2">
              <label>Tanggal Berikutnya</label>
              <input type="date" name="next_service_date" id="next_service_date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){
    let modal = new bootstrap.Modal($('#oilModal'));

    // Select2 untuk cari service yang mengandung oli
    $('#service_id').select2({
        dropdownParent: $('#oilModal'),
        placeholder: 'Cari service yang mengandung oli...',
        ajax: {
            url: "{{ route('oil_services.get_services_oli') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: function (data) {
                return { results: data.results };
            }
        }
    });

    // Ketika memilih service dari Select2, ambil nama oli otomatis
    $('#service_id').on('select2:select', function(e){
        let serviceId = e.params.data.id;

        if(serviceId){
            $.get(`/oil-services/get-oil-names/${serviceId}`, function(res){
                if(res.oils && res.oils.length > 0){
                    $('#oil_name').val(res.oils.join(', '));
                } else {
                    $('#oil_name').val('');
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Oli',
                        text: 'Tidak ada oli terdeteksi pada service ini.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).fail(function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengambil data oli.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    });

    // Tombol Tambah
    $('#btnAdd').click(function(){
        $('#oilForm')[0].reset();
        $('#oil_id').val('');
        $('#serviceSelectGroup').show();
        $('#modalTitle').text('Tambah Ganti Oli');
        $('#service_id').val(null).trigger('change');
        modal.show();
    });

    // Submit Form (Create/Update)
    $('#oilForm').submit(function(e){
        e.preventDefault();
        let id = $('#oil_id').val();
        let url = id ? `/oil-services/${id}` : `{{ route('oil_services.store') }}`;
        let method = id ? 'PUT' : 'POST';

        // === SweetAlert Loading ===
        Swal.fire({
            title: 'Sedang menyimpan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res){
                Swal.close();
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: id ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err){
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan. Pastikan semua data sudah terisi dengan benar.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    });

    // Edit Data
    $('.btnEdit').click(function(){
        let id = $(this).data('id');
        $.get(`/oil-services/${id}/edit`, function(data){
            $('#oil_id').val(data.id);
            $('#oil_name').val(data.oil_name);
            $('#km_service').val(data.km_service);
            $('#km_service_next').val(data.km_service_next);
            $('#next_service_date').val(data.next_service_date);
            $('#serviceSelectGroup').hide();
            $('#modalTitle').text('Edit Data Ganti Oli');
            modal.show();
        });
    });

    // Delete Data
    $('.btnDelete').click(function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang sudah dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // tampilkan loading dulu
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/oil-services/${id}`,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(){
                        Swal.close();
                        $(`#row-${id}`).remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Data berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(){
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal menghapus data.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush


