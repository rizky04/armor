<div class="modal fade" id="create" tabindex="-1" aria-labelledby="create" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                                <span class="text-danger" id="error-name"></span>
                                <small class="text-danger" id="error-name"></small>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="no_telp" id="no_telp" class="form-control">
                                <span class="text-danger" id="error-no_telp"></span>
                                <small class="text-danger" id="error-no_telp"></small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Plat Number</label>
                                <input type="text" name="plate_number" id="plate_number" class="form-control">
                                <span class="text-danger" id="error-plate_number"></span>
                                <small class="text-danger" id="error-plate_number"></small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="photo">Foto Kendaraan Customer (opsional)</label>
                                <input type="file" id="photo" name="photo" class="form-control">
                                <small id="error-photo" class="text-danger"></small>
                                 {{-- Preview Foto --}}
                                <div class="mt-2">
                                    <img id="customer-photo-preview"
                                        src=""
                                        alt="Preview Foto"
                                        class="img-fluid rounded shadow-sm"
                                        style="max-width: 120px; display: none; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2">Submit</button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
