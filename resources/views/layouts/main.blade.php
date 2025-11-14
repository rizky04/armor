<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - ARMOR MOTOR">
    <meta name="keywords" content="ARMOR MOTOR">
    <meta name="author" content="ARMOR MOTOR">
    <meta name="robots" content="noindex, nofollow">

    <title>Monitoring Service</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/assets/img/1234.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/animate.css') }}">


    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/owlcarousel/owl.theme.default.min.css') }}">


    <link rel="stylesheet" href="{{ asset('assets/assets/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/select2/css/select2.min.css') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/style.css') }}">
    {{-- <style>
        @media screen and (min-width: 992px) {
            .content {
                zoom: 0.9;
            }
        }
    </style> --}}
</head>

<body>
    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <div class="main-wrapper">


        {{-- header --}}
        @include('layouts.header')
        {{-- header --}}

        {{-- sidebar --}}


        @include('layouts.sidebar')
        {{-- sidebar --}}
        {{-- content --}}
        <div class="page-wrapper">
            <div class="content">
                @yield('content')
            </div>
        </div>
        {{-- content --}}
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var customerStoreUrl = "{{ route('customers.store') }}";
    // var customerSearchUrl = "{{ route('customers.search') }}";
    var transactionUrl = "{{ route('transactions.store') }}";
    var transactionDetailUrl = "{{ route('transactions.detail') }}";
    const deleteIcon = "{{ asset('assets/assets/img/icons/delete-2.svg') }}";
    var transactionDeleteUrl = "{{ route('transactions.destroy', ':id') }}";
      var transactionPrintUrl = "{{ route('transactions.print', ':id') }}";
</script>
{{-- <script src="{{ asset('assets/assets/js/customer.js') }}"></script> --}}
<script src="{{ asset('assets/assets/js/transaction.js') }}"></script>
<script src="{{ asset('assets/assets/js/editTransaction.js') }}"></script>

    {{-- <script src="{{ asset('assets/assets/js/jquery-3.6.0.min.js') }}"></script> --}}

    <script src="{{ asset('assets/assets/js/feather.min.js') }}"></script>

    <script src="{{ asset('assets/assets/js/jquery.slimscroll.min.js') }}"></script>

    <script src="{{ asset('assets/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/assets/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script src="{{ asset('assets/assets/js/bootstrap.bundle.min.js') }}"></script>

      <script src="{{ asset('assets/assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('assets/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>


    <script src="{{ asset('assets/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/assets/plugins/apexchart/chart-data.js') }}"></script>

    <script src="{{ asset('assets/assets/plugins/fileupload/fileupload.min.js') }}"></script>

    <script src="{{ asset('assets/assets/js/script.js') }}"></script>
    @stack('scripts')
</body>

</html>
