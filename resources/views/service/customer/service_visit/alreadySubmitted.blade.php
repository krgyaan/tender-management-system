<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>Report Already Submitted | Volks Energie</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coinex.css') }}" id="theme-style">
    <link rel="stylesheet" href="{{ asset('assets/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2-4.1.0/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/filepond.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/filepond-img-preview.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ckeditor.css') }}">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.0.0/ckeditor5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <style>
        .info-icon {
            font-size: 70px;
            color: #ffc107;
            animation: pop 0.6s ease;
        }

        .navbar {
            background: linear-gradient(135deg, #110703, #3a322d);
        }

        .navbar-brand img {
            height: 55px;
            transition: transform 0.3s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        @keyframes pop {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .info-message {
            font-weight: 700;
            color: #252524;
            margin-top: 20px;
            animation: fadeInDown 1s ease;
        }

        .btn-home {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 106, 0, 0.3);
            color: white;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="https://volksenergie.in/">
                <img src="https://volksenergie.in/wp-content/uploads/2024/09/VE_Logo-Final-Black_BG_Artboard-1.png"
                    alt="Volks Energie Logo">
            </a>
        </div>
    </nav>

    {{-- Info Content --}}
    <div class="container my-5">
        <div class="card p-5 text-center">
            <div class="card-body">
                <i class="fas fa-info-circle info-icon"></i>
                <h3 class="info-message">Service Visit Report Already Submitted</h3>
                <p class="mt-3">You have already submitted the service report for this complaint. Duplicate
                    submissions are not allowed.</p>
                <a href="https://volksenergie.in/" class="mt-3">Go to Home</a>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} Volks Energie. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>
