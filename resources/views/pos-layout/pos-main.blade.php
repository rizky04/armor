<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Car Wash Kharisma</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/assets/img/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/animate.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/owlcarousel/owl.theme.default.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/select2/css/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/bootstrap-datetimepicker.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets/plugins/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/assets/css/style.css') }}">
</head>

<body>

@yield('content')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var customerStoreUrl = "{{ route('customers.store') }}";
    var customerSearchUrl = "{{ route('customers.search') }}";
    var transactionUrl = "{{ route('transactions.store') }}";
    var transactionDetailUrl = "{{ route('transactions.detail') }}";
    const deleteIcon = "{{ asset('assets/assets/img/icons/delete-2.svg') }}";
    var transactionDeleteUrl = "{{ route('transactions.destroy', ':id') }}";
</script>
<script src="{{ asset('assets/assets/js/customer.js') }}"></script>
<script src="{{ asset('assets/assets/js/transaction.js') }}"></script>
<script src="{{ asset('assets/assets/js/editTransaction.js') }}"></script>

<script src="{{ asset('assets/assets/js/jquery-3.6.0.min.js') }}"></script>

<script src="{{ asset('assets/assets/js/feather.min.js') }}"></script>

<script src="{{ asset('assets/assets/js/jquery.slimscroll.min.js') }}"></script>

<script src="{{ asset('assets/assets/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('assets/assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/assets/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('assets/assets/plugins/select2/js/select2.min.js') }}"></script>

<script src="{{ asset('assets/assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

<script src="{{ asset('assets/assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>

<script src="{{ asset('assets/assets/js/script.js') }}"></script>
</body>

</html>
