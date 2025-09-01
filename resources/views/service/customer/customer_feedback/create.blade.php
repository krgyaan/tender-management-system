<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <link rel="shortcut icon" href="{{ asset('assets/images/ve_logo.png') }}" type="image/x-icon">
    <title>@yield('page-title', 'Customer Feedback') | Volks Energie</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/coinex.css') }}" id="theme-style">
    <link rel="stylesheet" href="{{ asset('assets/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-6.6.0/css/all.min.css') }}">

    @stack('styles')

    <style>
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(38, 38, 38, 0.2);
            animation: fadeInUp 0.8s ease;
        }

        .form-label {
            font-weight: 600;
            color: #383838;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #464646;
            box-shadow: 0 0 10px rgba(35, 34, 33, 0.25);
        }

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

        .form-title {
            font-weight: 700;
            color: #252524;
            animation: fadeInDown 1s ease;
        }

        footer {
            background: linear-gradient(90deg, #ff6a00, #ff9500);
            color: white;
            padding: 15px 0;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #110703, #3a322d);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="https://volksenergie.in/">
                <img src="https://volksenergie.in/wp-content/uploads/2024/09/VE_Logo-Final-Black_BG_Artboard-1.png"
                    alt="Volks Energie Logo" style="height: 55px;">
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="container my-5">
        <section>
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="mb-4 text-center form-title">Customer Feedback Form</h4>
                    @include('partials.messages')

                    <form action="{{ route('service_feedback.store') }}" method="POST" class="row g-3">
                        @csrf


                        {{-- Problem Resolved --}}
                        <div class="col-md-12">
                            <label class="form-label">
                                Is your problem resolved? / क्या आपकी समस्या का समाधान हो गया है?
                            </label>
                            <select name="problem_resolved" id="problemResolved" class="form-select" required>
                                <option value="" selected disabled>-- Select --</option>
                                <option value="1">Yes / हाँ</option>
                                <option value="0">No / नहीं</option>
                            </select>
                        </div>

                        {{-- Hidden section if NO is selected --}}
                        <div id="feedbackSection" style="display: none;">
                            {{-- Satisfaction --}}
                            <div class="col-md-12">
                                <label class="form-label">
                                    Are you satisfied with services provided? / क्या आप प्रदान की गई सेवाओं से संतुष्ट
                                    हैं?
                                </label>
                                <select name="satisfaction" class="form-select" required>
                                    <option value="" selected disabled>-- Select --</option>
                                    <option value="1">Yes / हाँ</option>
                                    <option value="0">No / नहीं</option>
                                </select>
                            </div>

                            {{-- Rating --}}
                            <div class="col-md-12">
                                <label class="form-label">
                                    How will you rate our services? / आप हमारी सेवाओं का मूल्यांकन कैसे करेंगे?
                                </label>
                                <input type="number" name="rating" class="form-control" min="1" max="10"
                                    placeholder="Rate between 1 to 10" required>
                            </div>

                            {{-- Suggestions --}}
                            <div class="col-md-12">
                                <label class="form-label">
                                    Suggestions (if any) / सुझाव, यदि आप देना चाहते हैं:
                                </label>
                                <textarea name="suggestions" rows="3" class="form-control" placeholder="Write your suggestions..."></textarea>
                            </div>
                        </div>

                        <input type="hidden" value={{ $complaint->id }} name="complaint_id">

                        {{-- Form Actions --}}
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit Feedback</button>
                            <a href="https://volksenergie.in/" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- Footer --}}
    {{-- <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} Volks Energie. All Rights Reserved.</p>
        </div>
    </footer> --}}

    {{-- JS Logic for conditional form --}}
    <script>
        document.getElementById('problemResolved').addEventListener('change', function() {
            const feedbackSection = document.getElementById('feedbackSection');
            if (this.value === '1') {
                feedbackSection.style.display = 'block';
            } else {
                feedbackSection.style.display = 'none';
            }
        });
    </script>
</body>

</html>
