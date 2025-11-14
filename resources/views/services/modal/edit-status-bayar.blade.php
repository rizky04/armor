 <div class="modal fade" id="statusModalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Ubah Status bayar Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-status-service-bayar">
                    <div class="modal-body">
                        <input type="hidden" id="status_service_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="new_status_bayar" name="status_bayar" class="form-control" required>
                                <option value="lunas">lunas</option>
                                <option value="hutang">hutang</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
