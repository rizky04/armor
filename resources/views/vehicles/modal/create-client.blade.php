 <div class="modal fade" id="clientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="clientForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_client">
                        <div class="mb-2">
                            <label>Nama Client</label>
                            <input type="text" id="nama_client" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>No. Telp</label>
                            <input type="text" id="no_telp" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>No. KTP</label>
                            <input type="text" id="no_ktp" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Alamat</label>
                            <textarea id="alamat" class="form-control" required></textarea>
                        </div>
                        {{-- <div class="mb-2">
                            <label>Hapus?</label>
                            <select id="hapus" class="form-control">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
