<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesPaymentController;
use App\Http\Controllers\Select2Controller;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServicePaymentController;
use App\Http\Controllers\ServiceReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\StokTransactionController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\OilServiceController;
use App\Http\Controllers\KasirReportController;

// Redirect root URL to /home if logged in, or to login otherwise
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login'); // or return view('welcome');
});

// Auth routes (login, register, forgot password, etc.)
Auth::routes();

// Home page after login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Protected routes (only accessible when logged in)
Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/data', [ProductController::class, 'getData'])->name('products.data');
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    // routes/web.php
    // Route::get('/spareparts/search', [ProductController::class, 'search'])->name('spareparts.search');
    Route::get('/vehicles/get', [VehicleController::class, 'getVehicles'])->name('vehicles.get');

    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/data', [VehicleController::class, 'getData'])->name('vehicles.data');
        Route::post('/store', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
    });

    Route::get('/select2/products', [Select2Controller::class, 'products'])->name('select2.products');
    Route::get('/select2/barang', [Select2Controller::class, 'barang'])->name('select2.barang');
    Route::get('/select2/barangSemua', [Select2Controller::class, 'barangSemua'])->name('select2.barangSemua');
    Route::get('/select2/vehicles', [Select2Controller::class, 'vehicles'])->name('select2.vehicles');
    Route::get('/select2/mechanics', [Select2Controller::class, 'mechanics'])->name('select2.mechanics');
    Route::get('/select2/clients', [Select2Controller::class, 'clients'])->name('select2.clients');
    Route::get('/select2/jasa', [Select2Controller::class, 'jasa'])->name('select2.jasa');

    Route::get('/jasa', [JasaController::class, 'index'])->name('jasa.index');
    Route::get('/jasa/data', [JasaController::class, 'data'])->name('jasa.data');
    Route::post('/jasa', [JasaController::class, 'store'])->name('jasa.store');
    Route::get('/jasa/{id}', [JasaController::class, 'show']);
    Route::put('/jasa/{id}', [JasaController::class, 'update']);
    Route::delete('/jasa/{id}', [JasaController::class, 'destroy']);
    Route::post('/jasa/store-ajax', [JasaController::class, 'storeAjax'])->name('jasa.store.ajax');

    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/data', [BarangController::class, 'getData'])->name('barang.data');
    Route::get('/barang/{id}', [BarangController::class, 'show']);
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales/store', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/data', [SalesController::class, 'data'])->name('sales.data');
    Route::get('/sales/{id}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::post('sales/{id}/update', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{id}/destroy', [SalesController::class, 'destroy'])->name('sales.destroy');
    Route::get('sales/{id}', [SalesController::class, 'show']);

    Route::post('/sales-payments/{sales}', [SalesPaymentController::class, 'store'])->name('sales-payments.store');
    Route::put('/sales-payments/{id}', [SalesPaymentController::class, 'update'])->name('sales-payments.update');
    Route::delete('/sales-payments/{id}', [SalesPaymentController::class, 'destroy'])->name('sales-payments.destroy');
    Route::get('/sales-payments/data', [SalesPaymentController::class, 'getData'])->name('sales-payments.data');
    Route::get('/sales/{id}/payment-detail', [SalesPaymentController::class, 'paymentDetail']);
    Route::get('/sales/{id}/print', [SalesController::class, 'print'])->name('sales.print');


    Route::get('/api/barang/by-code/{code}', [BarangController::class, 'getByCode']);
    Route::get('/api/barang/by-qr/{code}', [BarangController::class, 'getByQR']);


    Route::get('printQr', [BarangController::class, 'printQr'])->name('printQr');
    Route::get('/generateQr/{id}', [BarangController::class, 'generateQr'])->name('generateQr');

    Route::get('/client/data', [ClientController::class, 'data'])->name('client.data');
    Route::resource('client', ClientController::class);




    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/data', [ServiceController::class, 'getData'])->name('services.data');
        Route::get('/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::get('/{id}', [ServiceController::class, 'show'])->name('services.show');
        Route::get('/{id}/print', [ServiceController::class, 'print'])->name('services.print');
        Route::put('/{id}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/{id}/payment-detail', [ServiceController::class, 'paymentDetail']);
    });

         Route::get('fastServices', [ServiceController::class, 'fastService'])->name('services.fastServices');

    Route::get('detail/service/shows/{id}', [ServiceController::class, 'shows']);



Route::get('/stok-opname', [StokOpnameController::class, 'index'])->name('stok-opname.index');
Route::get('/stok-opname/data', [StokOpnameController::class, 'data'])->name('stok-opname.data');
Route::post('/stok-opname/update', [StokOpnameController::class, 'update'])->name('stok-opname.update');
// halaman riwayat stok opname
Route::get('/stok-opname/logs', [StokOpnameController::class, 'logs'])->name('stok-opname.logs');
Route::get('/stok-opname/logs/data', [StokOpnameController::class, 'logsData'])->name('stok-opname.logs.data');

Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
Route::get('/pembelian/data', [PembelianController::class, 'data'])->name('pembelian.data');
Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
Route::get('/pembelian/barang-info/{id}', [PembelianController::class, 'barangInfo']);
Route::delete('/pembelian/{id}', [PembelianController::class, 'destroy']);
Route::get('/pembelian/{id}/edit', [PembelianController::class, 'edit']);
Route::put('/pembelian/{id}', [PembelianController::class, 'update']);
Route::get('/select/barang', [PembelianController::class, 'barang'])->name('select.barang');


// Route::get('stok-transaksi', [StokTransactionController::class, 'index'])->name('stok-transaksi.index');
// Route::get('stok-transaksi/data', [StokTransactionController::class, 'data'])->name('stok-transaksi.data');
// Route::post('stok-transaksi/store', [StokTransactionController::class, 'store'])->name('stok-transaksi.store');
// Route::get('select2/barang', [BarangController::class, 'select2'])->name('select2.barang');

Route::prefix('stok-transaksi')->group(function () {
    Route::get('/', [StokTransactionController::class, 'index'])->name('stok-transaksi.index');
    Route::get('/data', [StokTransactionController::class, 'data'])->name('stok-transaksi.data');
    Route::post('/store', [StokTransactionController::class, 'store'])->name('stok-transaksi.store');
    Route::delete('/{id}', [StokTransactionController::class, 'destroy'])->name('stok-transaksi.destroy');
});

        // laporan


        Route::prefix('report')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');
        });


    Route::prefix('laporan')->group(function () {
        Route::get('/service', [ServiceReportController::class, 'serviceReport'])->name('services.report.service');
        Route::get('/jobs', [ServiceReportController::class, 'jobReport'])->name('services.report.jobs');
        Route::get('/spareparts', [ServiceReportController::class, 'sparepartReport'])->name('services.report.spareparts');
        Route::get('/mechanics', [ServiceReportController::class, 'mechanicReport'])->name('services.report.mechanics');
    });

    Route::get('/reports/service', [ReportController::class, 'serviceReport'])->name('reports.service');
    Route::get('/reports/sale', [ReportController::class, 'laporanPenjualanBarang'])->name('reports.sale');
    Route::get('/reports/laporanGabungan', [ReportController::class, 'laporanGabungan'])->name('reports.Gabungan');
    Route::get('/reports/mekanik', [ReportController::class, 'mekanik'])->name('reports.mekanik');
    Route::get('/reports/sold-items', [ReportController::class, 'soldItemsReport'])->name('reports.sold-items');


    Route::get('mechanics', [MechanicController::class, 'index'])->name('mechanics.index');
    Route::get('mechanics/data', [MechanicController::class, 'getData'])->name('mechanics.data');
    Route::post('mechanics', [MechanicController::class, 'store'])->name('mechanics.store');
    Route::get('mechanics/{id}', [MechanicController::class, 'show']);
    Route::put('mechanics/{id}', [MechanicController::class, 'update']);
    Route::delete('mechanics/{id}', [MechanicController::class, 'destroy']);

    Route::resource('category', CategoryController::class);

    Route::post('update/services/{service}/status', [ServiceController::class, 'updateStatus']);
    Route::post('services/{service}/statusBayar', [ServiceController::class, 'updateStatusBayar']);
    Route::post('service-payments/{serviceId}', [ServicePaymentController::class, 'store'])->name('service-payments.store');

    //service payment dan report
    Route::get('/service-payments/data', [ServicePaymentController::class, 'getData'])->name('service-payments.data');
    Route::get('/service/pembayaranService', [ServicePaymentController::class, 'index'])->name('pembayaran-service.index');

    // routes/web.php
Route::get('/sales-payments/data', [SalesReportController::class, 'getData'])->name('sales-payments.data');
Route::get('/sales-payments', [SalesReportController::class, 'index'])->name('sales-payments.index');





    Route::get('/service/laporanService', [ServicePaymentController::class, 'report'])->name('laporan.service');
    Route::get('/service/laporanService/data', [ServicePaymentController::class, 'data'])->name('laporan.service.data');





    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/data', [CustomerController::class, 'getData'])->name('customers.data');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/{id}/promo-check', [CustomerController::class, 'promoCheck']);

    Route::get('/transactions/daftarTransaksi', [TransactionController::class, 'daftarTransaksi'])->name('transactions.daftarTransaksi');
    Route::post('/transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/detail', [TransactionController::class, 'detail'])->name('transactions.detail');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    // routes/web.php

    Route::get('/transactions/data', [TransactionController::class, 'getData'])->name('transactions.data');
    Route::get('/transactions/{id}/print', [TransactionController::class, 'print'])->name('transactions.print');

    Route::resource('transactions', TransactionController::class);

    //report
    Route::prefix('reports')->group(function () {
        Route::get('/omzet', [ReportController::class, 'omzet'])->name('reports.omzet');
    });


    Route::get('/tampilanPromo', [PromoController::class, 'tampilanPromo'])->name('tampilanPromo');
    Route::get('/promos', [PromoController::class, 'index']);
    Route::post('/promos', [PromoController::class, 'store']);
    Route::put('/promos/{id}', [PromoController::class, 'update']);
    Route::patch('/promos/{id}/toggle', [PromoController::class, 'toggleActive']);
    Route::delete('/promos/{id}', [PromoController::class, 'destroy']);

    // routes/web.php
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/list', [PermissionController::class, 'list'])->name('permissions.list');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');


Route::get('/oil-services', [OilServiceController::class, 'index'])->name('oil_services.index');
Route::get('/oil-services/get-services-oli', [OilServiceController::class, 'getServicesWithOil'])->name('oil_services.get_services_oli');
Route::post('/oil-services/store', [OilServiceController::class, 'store'])->name('oil_services.store');
Route::get('/oil-services/{id}/edit', [OilServiceController::class, 'edit'])->name('oil_services.edit');
Route::put('/oil-services/{id}', [OilServiceController::class, 'update'])->name('oil_services.update');
Route::delete('/oil-services/{id}', [OilServiceController::class, 'destroy'])->name('oil_services.destroy');
Route::get('/oil-services/get-oil-names/{service_id}', [OilServiceController::class, 'getOilNamesByService'])->name('oil_services.get_oil_names');
Route::get('/oil-services/print/{id}', [OilServiceController::class, 'print'])->name('oil_services.print');
Route::get('/laporan/kasir', [KasirReportController::class, 'getData'])->name('laporan.kasir');
Route::get('/laporan/kasir/index', [KasirReportController::class, 'index'])->name('laporan.kasir.index');

});
