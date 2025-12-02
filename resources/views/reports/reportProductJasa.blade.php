@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Laporan Penjualan</h4>
            <h6>Barang & Jasa Service</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- FORM FILTER --}}
            <form id="filterForm" class="row mb-4">

                <div class="col-md-4">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Cari (Barang/Jasa)</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Nama barang / jasa...">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">🔍</button>
                </div>

            </form>

            {{-- TAB MENU --}}
            <ul class="nav nav-tabs mb-3" id="laporanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="barang-tab" data-bs-toggle="tab" data-bs-target="#barang"
                        type="button" role="tab">Barang Terjual</button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jasa-tab" data-bs-toggle="tab" data-bs-target="#jasa" type="button"
                        role="tab">Jasa Service</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="combined-tab" data-bs-toggle="tab" data-bs-target="#combined"
                        type="button" role="tab">Combined Report</button>
                </li>

            </ul>

            <div class="tab-content" id="laporanTabsContent">

                {{-- TAB BARANG --}}
                <div class="tab-pane fade show active" id="barang" role="tabpanel">

                    {{-- RINGKASAN BARANG --}}
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="card p-3">
                                <h6>Total Qty</h6>
                                <h3 id="b_qty">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3">
                                <h6>Total Penjualan</h6>
                                <h3 id="b_jual">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3">
                                <h6>Total Modal</h6>
                                <h3 id="b_modal">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3">
                                <h6>Total Profit</h6>
                                <h3 id="b_profit">0</h3>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL BARANG --}}
                    <h5>📋 Detail Barang Terjual</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr class="table-secondary text-center">
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Jual</th>
                                <th>Beli</th>
                                <th>Profit</th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                        <tbody id="barangTable"></tbody>
                    </table>

                    {{-- CHART BARANG --}}
                    <h5 class="mt-4">📊 Grafik Barang</h5>
                    <canvas id="chartBarang" height="100"></canvas>

                </div>

                {{-- TAB JASA --}}
                <div class="tab-pane fade" id="jasa" role="tabpanel">

                    {{-- RINGKASAN JASA --}}
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h6>Total Qty</h6>
                                <h3 id="j_qty">0</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h6>Total Omzet</h6>
                                <h3 id="j_omzet">0</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3">
                                <h6>Total Profit</h6>
                                <h3 id="j_profit">0</h3>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL JASA --}}
                    <h5>📋 Detail Jasa</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr class="table-secondary text-center">
                                <th>Nama Jasa</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody id="jasaTable"></tbody>
                    </table>

                    {{-- CHART JASA --}}
                    <h5 class="mt-4">📊 Grafik Jasa</h5>
                    <canvas id="chartJasa" height="100"></canvas>

                </div>
                {{-- TAB COMBINED --}}
                <div class="tab-pane fade" id="combined" role="tabpanel">

                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>Total Omzet Gabungan</h6>
                                <h3 id="c_omzet">0</h3>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6>Total Profit Gabungan</h6>
                                <h3 id="c_profit">0</h3>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">📊 Grafik Combined (Barang vs Jasa)</h5>
                    <canvas id="chartCombined" height="110"></canvas>

                </div>


            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartBarang = null;
        let chartJasa = null;
        let chartCombined = null;

        $("#filterForm").on("submit", function(e) {
            e.preventDefault();
            loadBarang();
            loadJasa();
            loadCombined();
        });

        function formatID(n) {
            return n.toLocaleString('id-ID');
        }

        // ==============================
        // ======== LOAD BARANG ========
        // ==============================
        function loadBarang() {
            let start = $("#start_date").val();
            let end = $("#end_date").val();
            let search = $("#search").val();

            $.get("/reportProduct", {
                start_date: start,
                end_date: end,
                search: search
            }, function(res) {

                let data = res.data;

                let bQty = 0,
                    bJual = 0,
                    bModal = 0,
                    bProfit = 0;
                $("#barangTable").empty();

                data.forEach(d => {
                    bQty += d.qty;
                    bJual += d.harga_jual * d.qty;
                    bModal += d.harga_beli * d.qty;
                    bProfit += d.profit;

                    $("#barangTable").append(`
                <tr class="text-center">
                    <td>${d.tanggal}</td>
                    <td>${d.kode_barang}</td>
                    <td>${d.nama_barang}</td>
                    <td>${d.qty}</td>
                    <td>${formatID(d.harga_jual)}</td>
                    <td>${formatID(d.harga_beli)}</td>
                    <td class="text-success">${formatID(d.profit)}</td>
                    <td>${d.sumber}</td>
                </tr>
            `);
                });

                $("#b_qty").text(bQty);
                $("#b_jual").text(formatID(bJual));
                $("#b_modal").text(formatID(bModal));
                $("#b_profit").text(formatID(bProfit));

                // Grafik
                if (chartBarang) chartBarang.destroy();
                let ctx = document.getElementById("chartBarang").getContext("2d");

                chartBarang = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.nama_barang),
                        datasets: [{
                            label: 'Qty Barang',
                            data: data.map(d => d.qty),
                            backgroundColor: 'rgba(13,110,253,0.5)',
                            borderColor: '#0d6efd',
                            borderWidth: 2
                        }]
                    }
                });
            });
        }


        // ==============================
        // ========= LOAD JASA ==========
        // ==============================
        function loadJasa() {
            let start = $("#start_date").val();
            let end = $("#end_date").val();
            let search = $("#search").val();

            $.get("/reportJasa", {
                start_date: start,
                end_date: end,
                search: search
            }, function(res) {

                let data = res.data;

                let jQty = 0,
                    jOmzet = 0,
                    jProfit = 0;
                $("#jasaTable").empty();

                data.forEach(d => {
                    jQty += d.qty;
                    jOmzet += d.total_jual;
                    jProfit += d.profit;

                    $("#jasaTable").append(`
                <tr class="text-center">
                    <td>${d.nama_jasa}</td>
                    <td>${d.qty}</td>
                    <td>${formatID(d.total_jual / d.qty)}</td>
                    <td>${formatID(d.total_jual)}</td>
                    <td class="text-success">${formatID(d.profit)}</td>
                </tr>
            `);
                });

                $("#j_qty").text(jQty);
                $("#j_omzet").text(formatID(jOmzet));
                $("#j_profit").text(formatID(jProfit));

                // Grafik
                if (chartJasa) chartJasa.destroy();
                let ctx = document.getElementById("chartJasa").getContext("2d");

                chartJasa = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.nama_jasa),
                        datasets: [{
                            label: 'Qty Jasa',
                            data: data.map(d => d.qty),
                            backgroundColor: 'rgba(255,165,0,0.5)',
                            borderColor: 'orange',
                            borderWidth: 2
                        }]
                    }
                });
            });
        }

        // ==============================
        // ======= LOAD COMBINED ========
        // ==============================

function loadCombined() {
    let start = $("#start_date").val();
    let end   = $("#end_date").val();
    let search = $("#search").val();

    $.get("/report-combined", { start_date:start, end_date:end, search:search }, function(res){

        // Ringkasan
        $("#c_omzet").text(formatID(res.combined.total_omzet));
        $("#c_profit").text(formatID(res.combined.total_profit));

        let bQty = res.barang.total_qty;
        let jQty = res.jasa.total_qty;

        // Grafik combined
        if(chartCombined) chartCombined.destroy();
        let ctx = document.getElementById("chartCombined").getContext("2d");

        chartCombined = new Chart(ctx, {
            type:'bar',
            data:{
                labels:["Barang", "Jasa"],
                datasets:[{
                    label:"Qty",
                    data:[bQty, jQty],
                    backgroundColor:[
                        "rgba(13,110,253,0.5)",
                        "rgba(255,165,0,0.5)"
                    ],
                    borderColor:["#0d6efd", "orange"],
                    borderWidth:2
                }]
            }
        });
    });
}

    </script>
@endpush
