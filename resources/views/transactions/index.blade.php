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
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="date" id="start-date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="end-date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" id="filter-btn">Filter</button>
                        <button class="btn btn-secondary" id="reset-btn">Reset</button>
                    </div>
                </div>

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

    @include('transactions.update')
    @include('transactions.detail')

    @push('scripts')
        <script>
            const productPrices = @json($products->pluck('price', 'id'));
            $(function() {
                const transactionUrl = "/transactions";

                const table = $("#transaction-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transactions.data') }}",
                        data: function(d) {
                            d.start_date = $("#start-date").val();
                            d.end_date = $("#end-date").val();
                        }
                    },
                    dom: 'Bfrtip', // 👉 tempat tombol export
                    buttons: [{
                            extend: 'excelHtml5',
                            text: '<img src="{{ asset('assets/assets/img/icons/excel.svg') }}" alt="img">',
                            className: 'btn btn-success btn-sm'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<img src="{{ asset('assets/assets/img/icons/pdf.svg') }}" alt="img">',
                            className: 'btn btn-danger btn-sm',
                            orientation: 'portrait',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            text: '<img src="{{ asset('assets/assets/img/icons/printer.svg') }}" alt="img">',
                            className: 'btn btn-secondary btn-sm'
                        },
                        {
                            text: '<i class="bi bi-arrow-clockwise"></i> Refresh',
                            className: 'btn btn-primary btn-sm',
                            action: function(e, dt, node, config) {
                                dt.ajax.reload(null, false); // reload data tanpa reset pagination
                            }
                        }
                    ],
                    columns: [{
                            data: "reference",
                            name: "reference"
                        },
                        {
                            data: "customer.name",
                            name: "customer.name"
                        },
                        {
                            data: "total",
                            name: "total"
                        },
                        {
                            data: "date",
                            name: "date"
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: "text-end",
                            render: function(data, type, row) {
                                return `
                        <button class="btn btn-sm edit-transaction" data-id="${row.id}">
                         <img src="{{ asset('assets/assets/img/icons/edit.svg') }}" alt="img">
                        </button>
                        <button class="btn btn-sm delete-transaction" data-id="${row.id}">
                            <img src="{{ asset('assets/assets/img/icons/delete.svg') }}" alt="img">
                        </button>
                        <button class="btn btn-sm detail-transaction" data-id="${row.id}">
                            <img src="{{ asset('assets/assets/img/icons/eye.svg') }}" alt="img">
                        </button>
                    `;
                            }
                        }
                    ]
                });

                // Filter tanggal
                $("#filter-btn").click(function() {
                    table.ajax.reload();
                });

                // Reset filter
                $("#reset-btn").click(function() {
                    $("#start-date").val("");
                    $("#end-date").val("");
                    table.ajax.reload();
                });


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

                function refreshTable() {
                    table.ajax.reload(null, false);
                }


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



                            console.log("data", trx.customer.photo)
                            if (trx.customer.photo) {
                                $("#detail-plate-photo").html(
                                    `<img src="/uploads/customer_car/${trx.customer.photo}" class="img-thumbnail" width="150">`
                                );
                            } else {
                                $("#detail-plate-photo").html("");
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
                            refreshTable();
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
                                    refreshTable();
                                },
                                error: function(xhr) {
                                    Swal.fire("Error", xhr.responseJSON?.message ||
                                        "Gagal hapus", "error");
                                }
                            });
                        }
                    });
                });

                function formatRupiah(angka) {
                    if (!angka) return "Rp 0";
                    return "Rp " + parseFloat(angka)
                        .toLocaleString("id-ID", {
                            minimumFractionDigits: 0
                        });
                }

                // klik tombol detail
                $(document).on("click", ".detail-transaction", function() {
                    let id = $(this).data("id");
                    $.get(`/transactions/${id}`, function(res) {
                        if (res.success) {
                            let trx = res.data;

                            // isi data ke modal
                            $("#detail-reference").text(trx.reference);
                            $("#detail-customer").text(trx.customer?.name || "-");
                            $("#detail-plate").text(trx.customer?.plate_number || "-");
                            $("#detail-total").text(formatRupiah(trx.total));
                            $("#detail-discount").text(formatRupiah(trx.discount));
                            $("#detail-tax").text(trx.tax + "%");
                            $("#detail-total-after-tax").text(formatRupiah(trx.total_after_tax));
                            $("#detail-cash").text(formatRupiah(trx.cash));
                            $("#detail-change").text(formatRupiah(trx.change));
                            $("#detail-date").text(trx.date);
                            $("#created-user").text(trx.created_by?.name || "-");
                            $("#updated-user").text(trx.updated_by?.name || "-");
                            // isi produk
                            let rows = "";
                            trx.items.forEach(it => {
                                rows += `
                    <tr>
                        <td>${it.product?.name || "-"}</td>
                        <td>${it.qty}</td>
                        <td>${formatRupiah(it.price)}</td>
                        <td>${formatRupiah(it.subtotal)}</td>
                    </tr>
                `;
                            });
                            $("#detail-items").html(rows);


                            if (trx.customer.photo) {
                                $("#detail-plate-photo-edit").html(
                                    `<img src="/uploads/customer_car/${trx.customer.photo}" class="img-thumbnail" width="150">`
                                );
                            } else {
                                $("#detail-plate-photo-edit").html("");
                            }

                            // tampilkan modal
                            $("#detailModal").modal("show");
                        }
                    });
                });


            });
        </script>
    @endpush
@endsection
