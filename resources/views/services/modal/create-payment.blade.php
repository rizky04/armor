<div class="modal fade" id="createpayment" tabindex="-1" aria-labelledby="createpaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">💳 Create Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="form-create-payment">
                <div class="modal-body">
                    <input type="hidden" id="payment_service_id" name="service_id">

                    <!-- ===================== DETAIL PEMBAYARAN ===================== -->
                    <div class="table-responsive mb-3">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">Detail Jasa</h6>
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nama Jasa</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="payment-detail-jasa">
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data jasa</td></tr>
                            </tbody>
                        </table>

                        <h6 class="fw-bold text-primary border-bottom pb-2 mt-3 mb-2">Detail Sparepart</h6>
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Nama Sparepart</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="payment-detail-sparepart">
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data sparepart</td></tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="fw-bold text-primary">Total Bayar:</span>
                            <span class="fw-bold fs-5 text-dark" id="sisa-bayar">Rp 0</span>
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
                            <input type="text" id="reference" name="reference" class="form-control" readonly>
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
                            <input type="text" id="input-bayar" name="amount_paid" class="form-control" required>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Kembalian</label>
                            <input type="text" id="kembalian" class="form-control" readonly>
                        </div>



                        <div class="col-lg-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-print-payment" class="btn btn-outline-success d-none">
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
