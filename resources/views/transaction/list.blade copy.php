<div class="modal fade" id="recents" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recent Transactions</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tabs-sets">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="purchase" role="tabpanel"
                            aria-labelledby="purchase-tab">
                            {{-- <div class="table-top d-flex justify-content-between align-items-center mb-3">

                                <div class="search-input">
                                    <input type="text" id="search-transaction" class="form-control"
                                        placeholder="Cari transaksi...">
                                </div>
                                <button id="refresh-transactions" class="btn btn-primary">Refresh</button>
                            </div> --}}


                            <div class="table-responsive">
                                {{-- <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Plat number</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transaction-body">
                                        <!-- data transaksi dari AJAX -->
                                    </tbody>
                                </table>
                                <nav>
                                    <ul id="pagination" class="pagination justify-content-center mt-3"></ul>
                                </nav> --}}
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

                                <table id="transaction-table" class="table table-bordered" style="width: 100%;">
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
                </div>

            </div>
        </div>
    </div>
</div>
@include('transactions.detail')
@push('scripts')
    <script>
        var transactionPrintUrl = "{{ route('transactions.print', ':id') }}";
        $(document).on('click', '.btn-print-transaction', function() {
            let id = $(this).data('id');
            window.open(transactionPrintUrl.replace(':id', id), '_blank');
        });
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
 <button class="btn btn-sm detail-transaction" data-id="${row.id}">
                            <img src="{{ asset('assets/assets/img/icons/eye.svg') }}" alt="img">
                        </button>
                        <button type="button" class="btn btn-sm btn-light btn-print-transaction" data-id="${row.id}">
            <img src="/assets/assets/img/icons/printer.svg" alt="print">
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

                        // foto plat
                        if (trx.plate_photo) {
                            $("#detail-plate-photo").html(
                                `<img src="/uploads/plates/${trx.plate_photo}" class="img-thumbnail" width="150">`
                            );
                        } else {
                            $("#detail-plate-photo").html("");
                        }

                        // tampilkan modal
                        $("#detailModal").modal("show");
                    }
                });
            });


        });
    </script>
@endpush
