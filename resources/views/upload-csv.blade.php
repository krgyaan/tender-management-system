<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>Volks Energie Pvt. Ltd.</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="This is Tender Management System of Volks Energie Pvt. Ltd. It is a web application that helps in managing tenders efficiently. It provides features like tender creation, submission, and tracking. The system is designed to streamline the tender management process and improve collaboration among team members.">
    <meta name="author" content="Volks Energie Pvt. Ltd.">
    <meta name="keywords"
        content="Tender Management System, Volks Energie Pvt. Ltd., Web Application, Tender Creation, Tender Submission, Tender Tracking, Tender Management, Collaboration, Team Management, Tender Process, Efficiency, Streamline, Web Development, Software Development, IT Solutions, Technology, Business Solutions">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coinex.css') }}">

    <style>
        main {
            background: hsla(186, 33%, 94%, 1);
            background: linear-gradient(90deg, hsla(186, 33%, 94%, 1) 0%, hsla(216, 41%, 79%, 1) 100%);
            background: -moz-linear-gradient(90deg, hsla(186, 33%, 94%, 1) 0%, hsla(216, 41%, 79%, 1) 100%);
            background: -webkit-linear-gradient(90deg, hsla(186, 33%, 94%, 1) 0%, hsla(216, 41%, 79%, 1) 100%);
            filter: progid: DXImageTransform.Microsoft.gradient(startColorstr="#EBF4F5", endColorstr="#B5C6E0", GradientType=1);
        }
    </style>
</head>

<body>
    <main>
        <div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-light">
            <div class="row shadow-lg rounded overflow-hidden" style="width: 90%; max-width: 1000px;">
                <!-- Left Column - Logo -->
                <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-dark">
                    <img src="{{ asset('assets/images/ve_logo_full.png') }}" alt="Logo" class="img-fluid p-5">
                </div>

                <!-- Right Column - Login Form -->
                <div class="col-md-6 bg-white p-5">

                    @if (Session::has('error'))
                        <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif
                    @if (Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('csv.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Select CSV File:</label>
                            <input class="form-control" type="file" name="file" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
