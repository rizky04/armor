@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>History Pembayaran Service</h4>
            <h6>Manage Pembayaran Service</h6>
        </div>
        {{-- <div class="page-btn">
            <a class="btn btn-added" href="{{ route('services.create') }}">+ Tambah Service</a>
        </div> --}}
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                {{-- <div class="col-md-4">
                    <input type="text" id="search" class="form-control"
                        placeholder="Cari nama pemilik / plat nomor...">
                </div> --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                      <input type="date" id="start_date" class="form-control" placeholder="Dari Tanggal">
                    </div>
                    <div class="col-md-3">
                      <input type="date" id="end_date" class="form-control" placeholder="Sampai Tanggal">
                    </div>
                    <div class="col-md-3">
                      <input type="text" id="search" class="form-control" placeholder="Cari nomor service / client">
                    </div>
                    <div class="col-md-3">
                      <button id="btn-filter" class="btn btn-primary w-100">Filter</button>
                    </div>
                  </div>
            </div>

            <div class="table-responsive text-center">
                <table class="table" id="payment-table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nomor Service</th>
                        <th>Client</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Metode</th>
                        <th>Note</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
            </div>
            <nav class="mt-3">
                <ul class="pagination" id="pagination"></ul>
            </nav>
            <div class="text-end mt-3">
                <h5>Total Semua: <span id="total-all" class="text-success">Rp 0</span></h5>
              </div>
        </div>
    </div>






    @push('scripts')
    <script>
        let currentPage = 1;
        let searchQuery = '';

        function loadPayments(page = 1, search = '', start = '', end = '') {
          currentPage = page;

          $.get("{{ route('service-payments.data') }}", {
              page: page,
              search: search,
              start_date: start,
              end_date: end
          }, function(res) {
              let rows = '';
              let i = (res.data.current_page - 1) * res.data.per_page;

              res.data.data.forEach(p => {
                  rows += `
                      <tr>
                          <td>${++i}</td>
                          <td>${p.payment_date}</td>
                          <td>${p.service?.nomor_service || '-'}</td>
                          <td>${p.service?.vehicle?.client?.nama_client || '-'}</td>
                          <td>${formatRupiah(p.amount_paid)}</td>
                           <td>${formatRupiah(p.change_amount)}</td>
                          <td>${p.payment_type}</td>
                          <td>${p.note || '-'}</td>
                      </tr>`;
              });

              $('#payment-table tbody').html(rows);
              $('#total-all').text(formatRupiah(res.total_all));

              // Pagination
              let pagination = '';
              for (let p = 1; p <= res.data.last_page; p++) {
                  pagination += `<li class="page-item ${res.data.current_page==p?'active':''}">
                      <a class="page-link" href="#" onclick="loadPayments(${p}, '${searchQuery}', $('#start_date').val(), $('#end_date').val())">${p}</a>
                  </li>`;
              }
              $('#pagination').html(pagination);
          });
        }

        $(document).ready(function() {
          loadPayments();

          $('#search').on('keyup', function() {
              searchQuery = $(this).val();
              loadPayments(1, searchQuery, $('#start_date').val(), $('#end_date').val());
          });

          $('#btn-filter').on('click', function() {
              loadPayments(1, searchQuery, $('#start_date').val(), $('#end_date').val());
          });
        });

        function formatRupiah(angka) {
          return new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              minimumFractionDigits: 0
          }).format(angka);
        }
        </script>

    @endpush
@endsection
