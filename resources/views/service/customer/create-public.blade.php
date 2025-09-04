<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>Register New Complaint | Volks Energie</title>
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
    @stack('styles')
    <style>
        /* Navbar */
        body {}

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

        /* Card */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(38, 38, 38, 0.2);
            animation: fadeInUp 0.8s ease;
        }

        /* Form labels */
        .form-label {
            font-weight: 600;
            color: #383838;
        }

        /* Input and textarea */
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #464646;
            box-shadow: 0 0 10px rgba(35, 34, 33, 0.25);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 106, 0, 0.3);
        }

        .btn-outline-secondary {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: #ffefe0;
            border-color: #ff6a00;
            color: #ff6a00;
        }

        /* Title */
        .form-title {
            font-weight: 700;
            color: #252524;
            animation: fadeInDown 1s ease;
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            color: white;
            padding: 15px 0;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

    {{-- Main Content --}}
    <div class="container my-5">

        @if ($complaintFromCookie)
            <div class="d-flex justify-content-center my-4">
                <div class="d-flex align-items-center shadow-sm rounded-3 px-4 py-3 bg-success bg-opacity-25 border-start border-4 border-success"
                    style="width: 100%;">
                    <i class="fas fa-clock fa-lg text-warning me-3"></i>
                    <div>
                        <span class="fw-bold text-dark">You’ve recently submitted a complaint</span><br>
                        <span class="text-secondary">Our team is already reviewing your request. You don’t need to
                            resubmit unless there’s a new issue.</span>
                    </div>
                </div>
            </div>
        @endif

        <section>
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="mb-4 text-center form-title">Register Your Complaints With Us</h4>
                    @include('partials.messages')

                    <form action="{{ route('register_complaint.store') }}" method="POST" class="row g-3"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-4">
                            <label class="form-label">Name <span class="fw-bold">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Organization</label>
                            <input type="text" name="organization" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phone<span class="fw-bold">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email<span class="fw-bold">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Site/Project Name<span class="fw-bold">*</span></label>
                            <input type="text" name="site_project_name" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">PO No.<span class="fw-bold">*</span></label>
                            <input type="text" name="po_no" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Site Location<span class="fw-bold">*</span></label>
                            <input type="text" name="site_location" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Upload Photo/Video</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Issue Faced<span class="fw-bold">*</span></label>
                            <textarea name="issue_faced" rows="4" class="form-control" placeholder="Please write the issue faced..."
                                required></textarea>
                        </div>

                        {{-- Form Actions --}}
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit Complaint</button>
                            <a href="https://volksenergie.in/" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- Footer --}}
    <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} Volks Energie. All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>
