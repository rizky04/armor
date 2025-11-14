<!-- Modal Edit Transaksi -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="edit-transaction-form" enctype="multipart/form-data">
        <input type="hidden" id="edit-transaction-id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editTransactionLabel">Edit Transaksi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="form-group">
              <label>Pelanggan</label>
              <input type="text" value="" id="edit-customer-id">
            </div>

            <div id="edit-items">
              <!-- list produk isi via JS -->
            </div>

            <div class="form-group">
              <label>Foto Plat Nomor</label><br>
              <img id="preview-plate" src="" alt="Preview" class="mb-2" width="150">
              <input type="file" class="form-control" id="edit-plate_photo" name="plate_photo">
            </div>

            <div class="form-group">
              <label>Total</label>
              <input type="text" id="edit-total" class="form-control" readonly>
            </div>

            <div class="form-group">
              <label>Diskon</label>
              <input type="number" id="edit-discount" class="form-control">
            </div>

            <div class="form-group">
              <label>Pajak</label>
              <input type="number" id="edit-tax" class="form-control">
            </div>

            <div class="form-group">
              <label>Total Setelah Pajak</label>
              <input type="text" id="edit-total-after-tax" class="form-control" readonly>
            </div>

            <div class="form-group">
              <label>Bayar (Cash)</label>
              <input type="number" id="edit-cash" class="form-control">
            </div>

            <div class="form-group">
              <label>Kembalian</label>
              <input type="text" id="edit-change" class="form-control" readonly>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
