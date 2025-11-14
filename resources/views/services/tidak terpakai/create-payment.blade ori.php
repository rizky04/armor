<div class="modal fade" id="createpayment" tabindex="-1" aria-labelledby="createpayment" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-create-payment">
                    <div class="modal-body">
                        <input type="hidden" id="payment_service_id" name="service_id">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Reference</label>
                                    <input type="text" id="reference" name="reference" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Jumlah Bayar</label>
                                    <input type="number" name="amount_paid" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select name="payment_type" class="form-select" required>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Note</label>
                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit">Submit</button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
