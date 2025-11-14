@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Transaksi</h4>
        <h6>Kelola transaksi pelanggan</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="transaction-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- data via ajax --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="transaction-form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Transaksi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="transaction-id">

            <div class="form-group">
                <label>Customer</label>
                <select id="customer-id" class="form-control" required>
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->plate_number }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Produk</label>
                <table class="table table-sm" id="product-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- item produk akan ditambahkan dinamis --}}
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-secondary" id="add-product-row">+ Tambah Produk</button>
            </div>

            <div class="form-group">
                <label>Total</label>
                <input type="number" id="total" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Bayar (Cash)</label>
                <input type="number" id="cash" class="form-control">
            </div>
            <div class="form-group">
                <label>Kembalian</label>
                <input type="number" id="change" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
    const productPrices = @json($products->pluck('price','id'));

$(function() {
    const transactionUrl = "/transactions";

    // load list transaksi
    function loadTransactions() {
        $.get(transactionUrl + "/detail", function(res) {
            let rows = '';
            res.data.forEach(trx => {
                rows += `
                <tr>
                    <td>${trx.reference}</td>
                    <td>${trx.customer.name}</td>
                    <td>${trx.total}</td>
                    <td>${trx.created_at}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-info edit-transaction" data-id="${trx.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger delete-transaction" data-id="${trx.id}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            });
            $("#transaction-table tbody").html(rows);
        });
    }
    loadTransactions();

    // tambah baris produk
    function productRow(item={}) {
        return `
        <tr>
            <td>
                <select class="form-control product-id">
                    <option value="">--Pilih--</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" ${item.product_id == {{ $p->id }} ? 'selected' : ''}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" class="form-control qty" value="${item.qty ?? 1}"></td>
            <td><input type="number" class="form-control price" value="${item.price ?? 0}"></td>
            <td><input type="number" class="form-control subtotal" value="${item.subtotal ?? 0}" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">x</button></td>
        </tr>`;
    }

    $("#add-product-row").click(function(){
        $("#product-table tbody").append(productRow());
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // kalau pilih produk -> isi harga
$(document).on('change', '.product-id', function() {
    let productId = $(this).val();
    let row = $(this).closest('tr');
    if(productId && productPrices[productId]){
        row.find('.price').val(productPrices[productId]);
    } else {
        row.find('.price').val(0);
    }
    calculateTotal();
});


    // hitung subtotal dan total
    function calculateTotal() {
        let total = 0;
        $("#product-table tbody tr").each(function() {
            let qty = parseInt($(this).find(".qty").val()) || 0;
            let price = parseFloat($(this).find(".price").val()) || 0;
            let sub = qty * price;
            $(this).find(".subtotal").val(sub);
            total += sub;
        });
        $("#total").val(total);
        let cash = parseFloat($("#cash").val()) || 0;
        $("#change").val(cash - total);
    }

    $(document).on('input', '.qty, .price, #cash', calculateTotal);

    // Edit transaksi
    $(document).on('click', '.edit-transaction', function() {
        let id = $(this).data("id");
        $.get(`${transactionUrl}/${id}`, function(res) {
            if(res.success) {
                let trx = res.data;
                $("#transaction-id").val(trx.id);
                $("#customer-id").val(trx.customer_id);

                $("#product-table tbody").empty();
                trx.items.forEach(it => {
                    $("#product-table tbody").append(productRow(it));
                });

                $("#total").val(trx.total);
                $("#cash").val(trx.cash);
                $("#change").val(trx.change);

                $("#transactionModal").modal("show");
            }
        });
    });

    // submit edit
    $("#transaction-form").submit(function(e) {
        e.preventDefault();
        let id = $("#transaction-id").val();
        let items = [];
        $("#product-table tbody tr").each(function(){
            items.push({
                product_id: $(this).find(".product-id").val(),
                qty: $(this).find(".qty").val(),
                price: $(this).find(".price").val(),
                subtotal: $(this).find(".subtotal").val()
            });
        });

        $.ajax({
            url: `${transactionUrl}/${id}`,
            method: "PUT",
            data: {
                customer_id: $("#customer-id").val(),
                total: $("#total").val(),
                discount: 0,
                tax: 0,
                total_after_tax: $("#total").val(),
                cash: $("#cash").val(),
                change: $("#change").val(),
                items: JSON.stringify(items),
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                Swal.fire("Sukses", res.message, "success");
                $("#transactionModal").modal("hide");
                loadTransactions();
            },
            error: function(xhr){
                Swal.fire("Error", xhr.responseJSON?.message || "Gagal update", "error");
            }
        });
    });

        // Hapus transaksi
    $(document).on('click', '.delete-transaction', function() {
        let id = $(this).data("id");
        Swal.fire({
            title: "Yakin hapus transaksi ini?",
            text: "Data tidak bisa dikembalikan setelah dihapus!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${transactionUrl}/${id}`,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire("Sukses", res.message, "success");
                        loadTransactions();
                    },
                    error: function(xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message || "Gagal hapus", "error");
                    }
                });
            }
        });
    });

});
</script>
@endpush

@endsection

