  {{-- Modal Edit --}}
    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form id="transaction-form" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Transaksi</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"> <span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="transaction-id">

                        <div class="form-group">
                            <label>Customer</label>
                            <select id="customer-id" class="form-control" required>
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} - {{ $c->plate_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Produk</label>
                            <table class="table table-sm" id="product-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- item produk dinamis --}}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-secondary" id="add-product-row">+ Tambah
                                Produk</button>
                        </div>

                        <div class="form-group">
                            <label>Total</label>
                            <input type="number" id="total" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Diskon</label>
                            <div class="input-group">
                                <input type="number" id="discount" class="form-control" value="0">
                                <select id="discount-type" class="form-select">
                                    <option value="rupiah">Rp</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Pajak</label>
                            <input type="text" id="tax" class="form-control" value="0">
                        </div>

                        <div class="form-group">
                            <label>Total Setelah Pajak</label>
                            <input type="number" id="total-after-tax" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Bayar (Cash)</label>
                            <input type="text" id="cash" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="number" id="change" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            {{-- <label>Foto Plat (opsional)</label>
                            <input type="file" id="plate-photo" name="plate_photo" class="form-control">
                            <small class="text-muted">Format: jpg, jpeg, png (maks 2MB)</small>
                            <div id="plate-photo-preview" class="mt-2"></div> --}}

                             <div id="detail-plate-photo" class="mt-3"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
