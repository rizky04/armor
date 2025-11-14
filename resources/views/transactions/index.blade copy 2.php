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
            <form id="transaction-form" enctype="multipart/form-data">
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
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->plate_number }}
                                    </option>
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
                                    {{-- item produk dinamis --}}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-secondary" id="add-product-row">+ Tambah
                                Produk</button>
                        </div>

                        <div class="form-group">
                            <label>Total</label>
                            <input type="number" id="total" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Diskon</label>
                            <div class="input-group">
                                <input type="number" id="discount" class="form-control" value="0">
                                <select id="discount-type" class="form-select">
                                    <option value="rupiah">Rp</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Pajak</label>
                            <input type="number" id="tax" class="form-control" value="0">
                        </div>

                        <div class="form-group">
                            <label>Total Setelah Pajak</label>
                            <input type="number" id="total-after-tax" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Bayar (Cash)</label>
                            <input type="number" id="cash" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="number" id="change" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Foto Plat (opsional)</label>
                            <input type="file" id="plate-photo" name="plate_photo" class="form-control">
                            <small class="text-muted">Format: jpg, jpeg, png (maks 2MB)</small>
                            <div id="plate-photo-preview" class="mt-2"></div>
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
            const productPrices = @json($products->pluck('price', 'id'));

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
                function productRow(item = {}) {
                    return `
        <tr>
            <td>
                <select class="form-control product-id">
                    <option value="">--Pilih--</option>
                    @foreach ($products as $p)
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

                $("#add-product-row").click(function() {
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
                    if (productId && productPrices[productId]) {
                        row.find('.price').val(productPrices[productId]);
                    } else {
                        row.find('.price').val(0);
                    }
                    calculateTotal();
                });

                // hitung subtotal dan total
                function calculateTotal() {
    let total = 0;

    // subtotal produk
    $("#product-table tbody tr").each(function() {
        let qty = parseInt($(this).find(".qty").val()) || 0;
        let price = parseFloat($(this).find(".price").val()) || 0;
        let sub = qty * price;
        $(this).find(".subtotal").val(sub);
        total += sub;
    });

    $("#total").val(total);

    // diskon
    let discount = parseFloat($("#discount").val()) || 0;
    let discountType = $("#discount-type").val();

    if (discountType === "percent") {
        discount = (total * discount) / 100;
    }

    let totalAfterDiscount = total - discount;

    // pajak dalam persen
    let taxPercent = parseFloat($("#tax").val()) || 0;
    let taxAmount = (totalAfterDiscount * taxPercent) / 100;

    let totalAfterTax = totalAfterDiscount + taxAmount;
    $("#total-after-tax").val(totalAfterTax);

    // kembalian
    let cash = parseFloat($("#cash").val()) || 0;
    $("#change").val(cash - totalAfterTax);
}



                $(document).on('input', '.qty, .price, #cash, #discount, #tax', calculateTotal);

                // Edit transaksi (load data lama)
                $(document).on('click', '.edit-transaction', function() {
                    let id = $(this).data("id");
                    $.get(`${transactionUrl}/${id}`, function(res) {
                        if (res.success) {
                            let trx = res.data;
                            $("#transaction-id").val(trx.id);
                            $("#customer-id").val(trx.customer_id);

                            $("#product-table tbody").empty();
                            trx.items.forEach(it => {
                                $("#product-table tbody").append(productRow(it));
                            });

                            $("#total").val(trx.total);
                            $("#discount").val(trx.discount);
                            $("#tax").val(trx.tax);
                            $("#total-after-tax").val(trx.total_after_tax);
                            $("#cash").val(trx.cash);
                            $("#change").val(trx.change);

                            // preview foto plat
                            if (trx.plate_photo) {
                                $("#plate-photo-preview").html(
                                    `<img src="/uploads/plates/${trx.plate_photo}" height="80" class="img-thumbnail">`
                                );
                            } else {
                                $("#plate-photo-preview").empty();
                            }

                            $("#transactionModal").modal("show");
                        }
                    });
                });

                // submit edit pakai FormData
                $("#transaction-form").submit(function(e) {
                    e.preventDefault();
                    let id = $("#transaction-id").val();
                    let items = [];
                    $("#product-table tbody tr").each(function() {
                        items.push({
                            product_id: $(this).find(".product-id").val(),
                            qty: $(this).find(".qty").val(),
                            price: $(this).find(".price").val(),
                            subtotal: $(this).find(".subtotal").val()
                        });
                    });

                    let formData = new FormData(this);
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("_method", "PUT");
                    formData.append("customer_id", $("#customer-id").val());
                    formData.append("total", $("#total").val());
                    formData.append("discount", $("#discount").val());
                    formData.append("tax", $("#tax").val());
                    formData.append("total_after_tax", $("#total-after-tax").val());
                    formData.append("cash", $("#cash").val());
                    formData.append("change", $("#change").val());
                    formData.append("items", JSON.stringify(items));

                    $.ajax({
                        url: `${transactionUrl}/${id}`,
                        method: "POST", // karena pakai _method=PUT
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            Swal.fire("Sukses", res.message, "success");
                            $("#transactionModal").modal("hide");
                            loadTransactions();
                        },
                        error: function(xhr) {
                            Swal.fire("Error", xhr.responseJSON?.message || "Gagal update",
                            "error");
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
                                    Swal.fire("Error", xhr.responseJSON?.message ||
                                        "Gagal hapus", "error");
                                }
                            });
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
