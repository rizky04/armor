 <div class="modal fade" id="vehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="vehicleForm" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kendaraan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="vehicle_id" name="vehicle_id">
                        <!-- select2 customer -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Plat Nomor</label>
                                    <input type="text" id="license_plate" name="license_plate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Client</label>
                                <select id="client_select" name="id_client" class="form-control"></select>                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Brand</label>
                                    <input type="text" id="brand" name="brand" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Type</label>
                                    <input type="text" id="type" name="type" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Engine Number</label>
                                    <input type="text" id="engine_number" name="engine_number" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Chassis Number</label>
                                    <input type="text" id="chassis_number" name="chassis_number" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Foto Plat</label>
                            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-2">
                            <img id="photo-preview" src="" style="max-width:120px; margin-top:5px; display:none;">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
