@extends('pos-layout.pos-main')
@section('content')
    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>
    <div class="main-wrappers">
        @include('layouts.header')
        <div class="page-wrapper ms-0">
            <div class="content">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success" role="alert">
                        {{ $message }}
                    </div>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
                <div class="row">
                    <div class="col-lg-8 col-sm-12 tabs_wrapper">
                        <div class="page-header ">
                            <div class="page-title">
                                <h4>Categories</h4>
                                <h6>Manage your purchases</h6>
                            </div>
                        </div>
                        <ul class=" tabs owl-carousel owl-theme owl-product  border-0 ">
                            @foreach ($categories as $key => $category)
                                <li class="{{ $key == 0 ? 'active' : '' }}" id="{{ $category->id }}">
                                    <div class="product-details ">
                                        <img src="{{ asset('uploads/category/' . $category->image) }}" alt="img"
                                            style="width:30px; height:30px; object-fit:cover;">
                                        <h6>{{ $category->name }}</h6>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tabs_container">
                            @foreach ($categories as $key => $category)
                                <div class="tab_content {{ $key == 0 ? 'show active' : '' }}"
                                    data-tab="{{ $category->id }}">
                                    <div class="row ">
                                        @foreach ($products->where('category_id', $category->id) as $product)
                                            <div class="col-lg-3 col-sm-6 d-flex ">
                                                <div class="productset flex-fill product-item" data-id="{{ $product->id }}"
                                                    data-name="{{ $product->name }}" data-price="{{ $product->price }}"
                                                    data-image="{{ asset('uploads/products/' . $product->image) }}"
                                                    >
                                                    <div class="productsetimg">
                                                        <img src="{{ asset('uploads/products/' . $product->image) }}"
                                                            alt="img">
                                                        <h6>Qty: 5.00</h6>
                                                        <div class="check-product">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                    <div class="productsetcontent">
                                                        <h5>Headphones</h5>
                                                        <h4>{{ $product->name }}</h4>
                                                        <h6>{{ $product->price }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12 ">
                        <div class="order-list">
                            <div class="orderid">
                                <h4>Order List</h4>
                                {{-- <h5>Transaction id : #65565</h5> --}}
                            </div>
                            {{-- <div class="actionproducts">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="deletebg confirm-text"><img
                                                src="{{ asset('assets/assets/img/icons/delete-2.svg') }}"
                                                alt="img"></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false"
                                            class="dropset">
                                            <img src="{{ asset('assets/assets/img/icons/ellipise1.svg') }}"
                                                alt="img">
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                            data-popper-placement="bottom-end">
                                            <li>
                                                <a href="#" class="dropdown-item">Action</a>
                                            </li>
                                            <li>
                                                <a href="#" class="dropdown-item">Another Action</a>
                                            </li>
                                            <li>
                                                <a href="#" class="dropdown-item">Something Elses</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div> --}}
                        </div>
                        <div class="card card-order">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="javascript:void(0);" class="btn btn-adds" data-bs-toggle="modal"
                                            data-bs-target="#create"><i class="fa fa-plus me-2"></i>Add Customer</a>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="select-split ">
                                            <div class="select-group w-100">
                                                <select class="form-control select2-ajax" id="customer_id"
                                                    name="customer_id" style="width: 100%;">
                                                    <option value="">Pilih Customer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-end">
                                            <div class="form-group">
                                                <label for="image">Foto kendaraan</label>
                                                <input type="file" id="image" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="split-card">
                            </div>
                            {{-- <div class="card-body pt-0">
                                <div class="totalitem">
                                    <h4>Total items : <span id="total-items">0</span></h4>
                                    <a href="javascript:void(0);" id="clear-all">Clear all</a>
                                </div>
                                <div class="product-table">

                                    <ul class="product-lists" id="product-list">
                                        <!-- Produk masuk ke sini -->
                                    </ul>
                                </div>
                            </div> --}}
                            <div class="card-body pt-0">
                                <div class="totalitem">
                                    <h4>Total items : <span id="total-items">0</span></h4>
                                    <a href="javascript:void(0);" id="clear-all">Clear all</a>
                                </div>
                                <div class="product-table">
                                    <ul class="list-group" id="product-list">
                                        <!-- Produk masuk ke sini -->
                                    </ul>
                                </div>
                                {{-- <div class="mt-3 text-end">
                                    <h5>Total Harga: <span id="total-price">0</span></h5>
                                </div> --}}
                            </div>

                            <div class="split-card">
                            </div>
                            <div class="card-body pt-0 pb-2">
                                <div class="setvalue">
                                    <ul>
                                        {{-- <li>
                                            <h5>Subtotal </h5>
                                            <h6 id="total-price">0</h6>
                                        </li>
                                        <li>
                                            <div class="form-group">
                                                <label>Tax (%)</label>
                                                <input type="number" id="tax" class="form-control" value="0" min="0">
                                            </div>
                                        </li>
                                        <li class="form-group">
                                            <h5>Total Setelah Tax </h5>
                                            <h6 id="total-after-tax">0</h6>
                                        </li>
                                        <li>
                                            <div class="form-group">
                                                <label>Bayar (Cash)</label>
                                                <input type="number" id="cash" class="form-control" value="0" min="0">
                                            </div>
                                        </li>
                                        <li class="form-group">
                                            <h5>Kembalian</h5>
                                            <h6 id="kembalian">0</h6>
                                        </li> --}}
                                        <div class="form-group">
                                            <label>Total Harga</label>
                                            <h4 id="total-price">0</h4>
                                        </div>

                                        <div class="form-group">
                                            <label for="discount_type">Discount Type</label>
                                            <select id="discount_type" class="form-control">
                                                <option value="percent">Percent (%)</option>
                                                <option value="nominal">Nominal (Rp)</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="discount">Discount</label>
                                            <input type="number" id="discount" class="form-control" value="0" min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Tax (%)</label>
                                            <input type="number" id="tax" class="form-control" value="0" min="0">
                                        </div>



                                        <div class="form-group">
                                            <label>Total Setelah Tax</label>
                                            <h4 id="total-after-tax">0</h4>
                                        </div>

                                        <div class="form-group">
                                            <label>Bayar (Cash)</label>
                                            <input type="number" id="cash" class="form-control" value="0" min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Kembalian</label>
                                            <h4 id="kembalian">0</h4>
                                        </div>

                                    </ul>
                                </div>
                                <div class="setvaluecash">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <img src="{{ asset('assets/assets/img/icons/cash.svg') }}"
                                                    alt="img" class="me-2">
                                                Cash
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <img src="{{ asset('assets/assets/img/icons/debitcard.svg') }}"
                                                    alt="img" class="me-2">
                                                Debit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <img src="{{ asset('assets/assets/img/icons/scan.svg') }}"
                                                    alt="img" class="me-2">
                                                Scan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="btn-totallabel">
                                    {{-- <h5>Checkout</h5>
                                    <h6>60.00$</h6> --}}
                                    <button id="save-transaction" class="btn btn-primary">Simpan Transaksi</button>
                                </div>
                                <div class="btn-pos">
                                    <ul>
                                        <li>
                                            <a class="btn"><img
                                                    src="{{ asset('assets/assets/img/icons/pause1.svg') }}"
                                                    alt="img" class="me-1">Hold</a>
                                        </li>
                                        <li>
                                            <a class="btn"><img
                                                    src="{{ asset('assets/assets/img/icons/edit-6.svg') }}"
                                                    alt="img" class="me-1">Quotation</a>
                                        </li>
                                        <li>
                                            <a class="btn"><img
                                                    src="{{ asset('assets/assets/img/icons/trash12.svg') }}"
                                                    alt="img" class="me-1">Void</a>
                                        </li>
                                        <li>
                                            <a class="btn"><img
                                                    src="{{ asset('assets/assets/img/icons/wallet1.svg') }}"
                                                    alt="img" class="me-1">Payment</a>
                                        </li>
                                        <li>
                                            <a class="btn" data-bs-toggle="modal" data-bs-target="#recents"><img
                                                    src="{{ asset('assets/assets/img/icons/transcation.svg') }}"
                                                    alt="img" class="me-1">
                                                Transaction</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="calculator" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Define Quantity</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="calculator-set">
                        <div class="calculatortotal">
                            <h4>0</h4>
                        </div>
                        <ul>
                            <li>
                                <a href="javascript:void(0);">1</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">2</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">3</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">4</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">5</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">6</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">7</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">8</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">9</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="btn btn-closes"><img
                                        src="{{ asset('assets/assets/img/icons/close-circle.svg') }}"
                                        alt="img"></a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">0</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="btn btn-reverse"><img
                                        src="{{ asset('assets/assets/img/icons/reverse.svg') }}"
                                        alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="holdsales" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hold order</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="hold-order">
                        <h2>4500.00</h2>
                    </div>
                    <div class="form-group">
                        <label>Order Reference</label>
                        <input type="text">
                    </div>
                    <div class="para-set">
                        <p>The current order will be set on hold. You can retreive this order from the pending order
                            button. Providing a reference to it might help you to identify the order more quickly.</p>
                    </div>
                    <div class="col-lg-12">
                        <a class="btn btn-submit me-2">Submit</a>
                        <a class="btn btn-cancel" data-bs-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Product Price</label>
                                <input type="text" value="20">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Product Price</label>
                                <select class="select">
                                    <option>Exclusive</option>
                                    <option>Inclusive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label> Tax</label>
                                <div class="input-group">
                                    <input type="text">
                                    <a class="scanner-set input-group-text">
                                        %
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Discount Type</label>
                                <select class="select">
                                    <option>Fixed</option>
                                    <option>Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Discount</label>
                                <input type="text" value="20">
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Sales Unit</label>
                                <select class="select">
                                    <option>Kilogram</option>
                                    <option>Grams</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <a class="btn btn-submit me-2">Submit</a>
                        <a class="btn btn-cancel" data-bs-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    <div class="modal fade" id="delete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Deletion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="delete-order">
                        <img src="{{ asset('assets/assets/img/icons/close-circle1.svg') }}" alt="img">
                    </div>
                    <div class="para-set text-center">
                        <p>The current order will be deleted as no payment has been <br> made so far.</p>
                    </div>
                    <div class="col-lg-12 text-center">
                        <a class="btn btn-danger me-2">Yes</a>
                        <a class="btn btn-cancel" data-bs-dismiss="modal">No</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="recents" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recent Transactions</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="tabs-sets">
                        {{-- <ul class="nav nav-tabs" id="myTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab"
                                    data-bs-target="#purchase" type="button" aria-controls="purchase"
                                    aria-selected="true" role="tab">Purchase</button>
                            </li>
                        </ul> --}}
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="purchase" role="tabpanel"
                                aria-labelledby="purchase-tab">
                                <div class="table-top d-flex justify-content-between align-items-center mb-3">
                                    <!-- Search manual -->
                                    <div class="search-input">
                                        <input type="text" id="search-transaction" class="form-control" placeholder="Cari transaksi...">
                                    </div>
                                    <button id="refresh-transactions" class="btn btn-primary">Refresh</button>
                                </div>

                                {{-- <div class="d-flex mb-3">
                                    <input type="date" id="start-date" class="form-control me-2">
                                    <input type="date" id="end-date" class="form-control me-2">
                                    <button id="filter-transaction" class="btn btn-primary me-2">Filter</button>
                                    <button id="refresh-transaction" class="btn btn-secondary">Reset</button>
                                </div> --}}

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="transaction-body">
                                            <!-- data transaksi dari AJAX -->
                                        </tbody>
                                    </table>
                                    <nav>
                                        <ul id="pagination" class="pagination justify-content-center mt-3"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
 {{-- <ul class="product-lists">
                                        <li>
                                            <div class="productimg">
                                                <div class="productimgs">
                                                    <img src="{{ asset('assets/assets/img/product/product30.jpg') }}"
                                                        alt="img">
                                                </div>
                                                <div class="productcontet">
                                                    <h4>Pineapple
                                                        <a href="javascript:void(0);" class="ms-2" data-bs-toggle="modal"
                                                            data-bs-target="#edit"><img
                                                                src="{{ asset('assets/assets/img/icons/edit-5.svg') }}"
                                                                alt="img"></a>
                                                    </h4>
                                                    <div class="productlinkset">
                                                        <h5>PT001</h5>
                                                    </div>
                                                    <div class="increment-decrement">
                                                        <div class="input-groups">
                                                            <input type="button" value="-"
                                                                class="button-minus dec button">
                                                            <input type="text" name="child" value="0"
                                                                class="quantity-field">
                                                            <input type="button" value="+"
                                                                class="button-plus inc button ">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>3000.00 </li>
                                        <li><a class="confirm-text" href="javascript:void(0);"><img
                                                    src="{{ asset('assets/assets/img/icons/delete-2.svg') }}"
                                                    alt="img"></a></li>
                                    </ul> --}}