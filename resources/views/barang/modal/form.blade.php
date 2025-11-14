<!-- Modal Create/Edit -->
<div class="modal fade" id="barangModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="barangForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_barang">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Kode Barang</label>
                    <input type="text" id="kode_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Nama Barang</label>
                    <input type="text" id="nama_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Merk</label>
                    <input type="text" id="merk_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Lokasi</label>
                    <input type="text" id="lokasi" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Stok</label>
                    <input type="number" id="stok_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Pagu</label>
                    <input type="number" id="pagu" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Harga Kulak</label>
                    <input type="number" id="harga_kulak" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Harga Jual</label>
                    <input type="number" id="harga_jual" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Jenis</label>
                    <input type="text" id="jenis" class="form-control">
                </div>
                 <div class="col-md-6 mb-2">
                    <label>distributor</label>
                    <input type="text" id="distributor" class="form-control">
                </div>
                {{-- <div class="col-md-6 mb-2">
                    <label>Hapus?</label>
                    <select  id="hapus" class="form-control">
                        <option value="0" selected>Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div> --}}
                <input type="hidden" value="0" id="hapus">
                <div class="col-md-12 mb-2">
                    <label>Keterangan</label>
                    <textarea id="keterangan" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
