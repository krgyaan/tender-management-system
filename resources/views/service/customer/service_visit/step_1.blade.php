<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>Service Visit Report | Volks Energie</title>
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
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #110703, #3a322d);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="https://volksenergie.in/">
                <img src="https://volksenergie.in/wp-content/uploads/2024/09/VE_Logo-Final-Black_BG_Artboard-1.png"
                    alt="Volks Energie Logo" style="height:55px;">
            </a>
        </div>
    </nav>

    <!-- Service Visit Form -->
    <div class="container my-5">
        <section>
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Enter Service Visit Report</h4>
                    @include('partials.messages')

                    <form action="{{ route('service_visit.public.store.step1') }}" method="POST" class="row g-3"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value={{ $complaintId }} name="complaint_id">
                        <!-- Visit Done -->
                        <div class="col-md-4">
                            <label class="form-label">Service Visit Done</label>
                            <select name="visit_done" class="form-control" required>
                                <option value="" disabled
                                    {{ old('visit_done', session("service_visit_form.$complaintId.visit_done")) ? '' : 'selected' }}>
                                    Choose...
                                </option>
                                <option value="1"
                                    {{ old('visit_done', session("service_visit_form.$complaintId.visit_done")) == '1' ? 'selected' : '' }}>
                                    Yes
                                </option>
                                <option value="0"
                                    {{ old('visit_done', session("service_visit_form.$complaintId.visit_done")) == '0' ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>


                        <!-- Visit Date & Time -->
                        <div class="col-md-4">
                            <label class="form-label">Visit Date & Time</label>
                            <input type="datetime-local" name="visit_datetime" class="form-control"
                                value="{{ old('visit_datetime', session("service_visit_form.$complaintId.visit_datetime")) }}"
                                required>
                        </div>

                        <!-- Resolution Done -->
                        <div class="col-md-4">
                            <label class="form-label">Resolution Done</label>
                            <select name="resolution_done" class="form-control" required>
                                <option value="" disabled
                                    {{ old('resolution_done', session("service_visit_form.$complaintId.resolution_done")) ? '' : 'selected' }}>
                                    Choose...
                                </option>
                                <option value="1"
                                    {{ old('resolution_done', session("service_visit_form.$complaintId.resolution_done")) == '1' ? 'selected' : '' }}>
                                    Yes
                                </option>
                                <option value="0"
                                    {{ old('resolution_done', session("service_visit_form.$complaintId.resolution_done")) == '0' ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" rows="3" class="form-control" placeholder="Enter remarks here">{{ old('remarks', session("service_visit_form.$complaintId.remarks")) }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Upload Photos</button>
                            {{-- <a href="https://volksenergie.in/" class="btn btn-outline-secondary px-4">Cancel</a> --}}
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>


    {{-- Footer --}}
    <footer class="text-center" style="  position: fixed; bottom: 0; left: 0; right:0; width:100%">
        <div class="container">
            <p>&copy; {{ date('Y') }} Volks Energie. All Rights Reserved.</p>
        </div>
    </footer>
</body>



</html>
