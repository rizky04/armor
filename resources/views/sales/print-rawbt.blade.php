<!DOCTYPE html>
<html>
<head>
    <title>Print ke RawBT</title>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // otomatis lempar ke RawBT saat halaman dibuka
            window.location.href = "rawbt:data:application/pdf;base64,{{ $base64 }}";
        });
    </script>
</head>
<body>
    <p>Struk sedang dikirim ke RawBT...</p>
</body>
</html>


