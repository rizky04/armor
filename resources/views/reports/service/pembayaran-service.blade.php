@extends('layouts.main')

@section('content')
<div class="page-header">
  <div class="page-title">
    <h4>History Pembayaran Service</h4>
    <h6>Manage Pembayaran Service</h6>
  </div>
</div>

<div class="card">
  <div class="card-body">
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

    <div class="table-responsive text-center">
      <table class="table table-bordered" id="payment-table">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Detail</th>
            <th>Tanggal</th>
            <th>Nomor Service</th>
            <th>Client</th>
            <th>Jumlah Bayar</th>
            <th>Kembalian</th>
            <th>Metode</th>
            <th>Aksi</th>
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
      page, search,
      start_date: start,
      end_date: end
  }, function(res) {
       // lihat isi response

      let rows = '';
      let i = (res.data.current_page - 1) * res.data.per_page;
console.log(res.data.data);
      res.data.data.forEach(p => {
          const s = p.service || {};
          const v = s.vehicle || {};
          const c = v.client || {};
          rows += `
            <tr>
              <td>${++i}</td>
               <td>
                <button class="btn btn-sm btn-outline-info toggle-detail" data-id="${s.id}">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </td>
              <td>${p.payment_date || '-'}</td>
              <td>${s.nomor_service || '-'}</td>
              <td>${c.nama_client || '-'}</td>
              <td>${formatRupiah(p.amount_paid)}</td>
              <td>${formatRupiah(p.change_amount)}</td>
              <td>${p.payment_type || '-'}</td>
              <td>${p.note || '-'}</td>

            </tr>
            <tr class="detail-row d-none" id="detail-${s.id}">
              <td colspan="9" class="bg-light text-start">
                <strong>Detail Service</strong>
                <table class="table table-sm mt-2 mb-1">
                  <thead>
                    <tr>
                      <th>Jenis</th>
                      <th>Nama</th>
                      <th>Qty</th>
                      <th>Harga</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${(s.jobs || []).map(j => `
                      <tr>
                        <td>Jasa</td>
                        <td>${j.jasa?.nama_jasa || '-'}</td>
                        <td>${j.qty || 1}</td>
                        <td>${formatRupiah(j.price)}</td>
                        <td>${formatRupiah(j.price * (j.qty || 1))}</td>
                      </tr>
                    `).join('')}
                    ${(s.spareparts || []).map(sp => `
                      <tr>
                        <td>Sparepart</td>
                        <td>${sp.barang?.nama_barang || '-'}</td>
                        <td>${sp.qty}</td>
                        <td>${formatRupiah(sp.price)}</td>
                        <td>${formatRupiah(sp.subtotal)}</td>
                      </tr>
                    `).join('')}
                  </tbody>
                   <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total :</th>
                                    <th>${formatRupiah(s.total_cost)}</th>
                                </tr>
                            </tfoot>
                </table>
              </td>
            </tr>
          `;
      });

      $('#payment-table tbody').html(rows);
      $('#total-all').text(formatRupiah(res.total_all));

      // Pagination
      let pagination = '';
      for (let p = 1; p <= res.data.last_page; p++) {
          pagination += `
            <li class="page-item ${res.data.current_page == p ? 'active' : ''}">
              <a class="page-link" href="#" onclick="loadPayments(${p}, '${searchQuery}', $('#start_date').val(), $('#end_date').val())">${p}</a>
            </li>`;
      }
      $('#pagination').html(pagination);
  });
}

// Toggle detail
$(document).on('click', '.toggle-detail', function() {
  const id = $(this).data('id');
  $(`#detail-${id}`).toggleClass('d-none');
});

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
