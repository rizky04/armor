<div class="modal fade" id="createSalesPayment" tabindex="-1" aria-labelledby="createSalesPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">💳 Create Sales Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="form-create-sales-payment">
                <div class="modal-body">
                    <input type="hidden" id="payment_sales_id" name="id_sales">

                    <!-- ===================== DETAIL PEMBAYARAN ===================== -->
                    <div class="table-responsive mb-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">Detail Barang</h6>
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="payment-detail-items">
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data barang</td></tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="fw-bold text-primary">Total Bayar:</span>
                            <span class="fw-bold fs-5 text-dark" id="sisa-bayar-sales">Rp 0</span>
                        </div>
                    </div>

                    <!-- ===================== INPUT FORM ===================== -->
                    <div class="row g-3">
                        {{-- <div class="col-lg-4">
                            <label class="form-label">Tanggal Pembayaran</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div> --}}

                        <div class="col-lg-6">
                            <label class="form-label">Reference</label>
                            <input type="text" id="reference-sales" name="reference" class="form-control" readonly>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Jumlah Bayar</label>
                            <input type="text" id="input-bayar-sales" name="amount_paid" class="form-control" required>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Kembalian</label>
                            <input type="text" id="kembalian-sales" class="form-control" readonly>
                        </div>

                        <div class="col-lg-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-print-sales-payment" class="btn btn-outline-success d-none">
                        <i class="fa-solid fa-print me-2"></i> Print
                    </button>
                    <div>
                        <button type="submit" class="btn btn-primary px-4">💾 Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
