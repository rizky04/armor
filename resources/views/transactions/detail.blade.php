<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr>
                        <th>Reference</th>
                        <td id="detail-reference"></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td id="detail-customer"></td>
                    </tr><tr>
                        <th>Plate Number</th>
                        <td id="detail-plate"></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td id="detail-total"></td>
                    </tr>
                    <tr>
                        <th>Diskon</th>
                        <td id="detail-discount"></td>
                    </tr>
                    <tr>
                        <th>Pajak</th>
                        <td id="detail-tax"></td>
                    </tr>
                    <tr>
                        <th>Total Setelah Pajak</th>
                        <td id="detail-total-after-tax"></td>
                    </tr>
                    <tr>
                        <th>Bayar</th>
                        <td id="detail-cash"></td>
                    </tr>
                    <tr>
                        <th>Kembalian</th>
                        <td id="detail-change"></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td id="detail-date"></td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td id="created-user"></td>
                    </tr>
                     <tr>
                        <th>Updated By</th>
                        <td id="updated-user"></td>
                    </tr>

                </table>

                <h6>Produk</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detail-items">
                        <!-- Produk akan dimasukkan via JS -->
                    </tbody>
                </table>

                <div id="detail-plate-photo-edit" class="mt-3">

                </div>
            </div>
        </div>
    </div>
</div>
