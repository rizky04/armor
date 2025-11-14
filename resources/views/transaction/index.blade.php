{{-- @extends('pos-layout.pos-main') --}}
@extends('layouts.main')
@section('content')
    {{-- <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div> --}}
    {{-- <div class="main-wrappers"> --}}
        {{-- @include('layouts.header') --}}
        {{-- <div class="page-wrapper ms-0"> --}}
            {{-- <div class="content"> --}}
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
                                                    data-image="{{ asset('uploads/products/' . $product->image) }}" data-category="{{ $product->category->name }}"
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
                                                        {{-- <h5>{{ $product->category->name }}</h5> --}}
                                                        <h4>{{ $product->name }}</h4>
                                                        <h6>Rp {{ number_format($product->price, 0, ',', '.') }}</h6>
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
                                    {{-- <div class="col-12">
                                        <div class="text-end">
                                            <div class="form-group">
                                                <label for="image">Foto kendaraan</label>
                                                <input type="file" id="plate_photo" name="plate_photo" class="form-control" accept="image/*" hidden>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <input type="file" id="plate_photo" name="plate_photo" class="form-control" accept="image/*" hidden>
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
                                    <a id="clear-all">Clear all</a>
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
                                            <input type="text" id="discount" class="form-control" value="0" min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Tax (%)</label>
                                            <input type="text" id="tax" class="form-control" value="0" min="0">
                                        </div>



                                        <div class="form-group">
                                            <label>Grad Total</label>
                                            <h4 id="total-after-tax">0</h4>
                                        </div>

                                        <div class="form-group">
                                            <label>Bayar (Cash)</label>
                                            <input type="text" id="cash" class="form-control" value="0" min="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Kembalian</label>
                                            <h4 id="kembalian">0</h4>
                                        </div>

                                    </ul>
                                </div>
                                {{-- <div class="setvaluecash">
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
                                                Debit/Transfer
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <img src="{{ asset('assets/assets/img/icons/scan.svg') }}"
                                                    alt="img" class="me-2">
                                                QRIS
                                            </a>
                                        </li>
                                    </ul>
                                </div> --}}
                                <div class="setvaluecash">
    <ul class="list-unstyled payment-options d-flex gap-3">
        <li>
            <label class="paymentmethod flex-column">
                <input type="radio" name="payment_method" value="cash" hidden>
                <img src="{{ asset('assets/assets/img/icons/cash.svg') }}" alt="Cash" class="payment-icon">
                <span>Cash</span>
            </label>
        </li>
        <li>
            <label class="paymentmethod flex-column">
                <input type="radio" name="payment_method" value="debit" hidden>
                <img src="{{ asset('assets/assets/img/icons/debitcard.svg') }}" alt="Debit" class="payment-icon">
                <span>Debit/Transfer</span>
            </label>
        </li>
        <li>
            <label class="paymentmethod flex-column">
                <input type="radio" name="payment_method" value="qris" hidden>
                <img src="{{ asset('assets/assets/img/icons/scan.svg') }}" alt="QRIS" class="payment-icon">
                <span>QRIS</span>
            </label>
        </li>
        <input type="hidden" name="payment_method" id="payment_method_hidden">
    </ul>
</div>

                                <div class="form-group text-center">
                                    <button id="save-transaction" class="btn btn-primary">Simpan Transaksi</button>
                                </div>
                                {{-- <div class="btn-totallabel">
                                    <h5>Grand Total</h5>
                                    <h6>60.00$</h6>
                                    <button id="save-transaction" class="btn btn-primary">Simpan Transaksi</button>
                                </div> --}}
                                <div class="btn-pos">
                                    <ul class="d-flex justify-content-center">
                                        {{-- <li>
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
                                        </li> --}}
                                        <li>
                                            <a class="btn" data-bs-toggle="modal" data-bs-target="#recents"><img
                                                    src="{{ asset('assets/assets/img/icons/transcation.svg') }}"
                                                    alt="img" class="me-1">
                                                Transaction</a>
                                                {{-- <a class="btn" href="{{route('transactions.daftarTransaksi')}}"><img
                                                    src="{{ asset('assets/assets/img/icons/transcation.svg') }}"
                                                    alt="img" class="me-1">
                                                Transaction</a> --}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
        {{-- </div> --}}
    {{-- </div> --}}
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
  @include('transaction.create-customer')

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
    @include('transaction.list')
    @include('transaction.edit')
@endsection
