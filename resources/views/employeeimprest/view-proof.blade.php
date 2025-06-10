@extends('layouts.app')
@section('page-title', 'Employee Imprest Vouchers')
@section('content')
    <div class="page-wrapper">
        @include('partials.messages')
        <div class="page-content">
            <div class="row">
                <div class="col-md-12 mx-auto mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ URL::previous() }}" class="btn btn-outline-danger btn-sm">back</a>
                    </div>
                    <div class="card">
                        @include('partials.messages')
                        <div class="card-body">
                            <h5 class="card-title">Proof</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                @php
                                    $string = Crypt::decrypt($proof);
                                    if ($string) {
                                        // Merge inner arrays
                                        $proofs = str_replace('],[', ',', $string);
                                        // Remove outer brackets
                                        $clean = trim($proofs, '[]');
                                        $array = explode(',', $clean);
                                        // Remove surrounding quotes
                                        $pdf = array_map(function ($item) {
                                            return trim($item, '"');
                                        }, $array);
                                        foreach ($pdf as $key => $file) {
                                            $path = asset('uploads/employeeimprest/' . $file);
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            if ($ext == 'pdf') {
                                                echo '<a type="button" data-path="' .
                                                    $path .
                                                    '">PDF-' .
                                                    $key +
                                                    1 .
                                                    '</a>';
                                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                echo '<a type="button" data-path="' .
                                                    $path .
                                                    '">Image-' .
                                                    $key +
                                                    1 .
                                                    '</a>';
                                            } else {
                                                echo "<p>Unknown file type - $file</p>";
                                            }
                                        }
                                    } else {
                                        echo '<p>No proof found</p>';
                                    }
                                @endphp
                            </div>

                            <div class="pt-4" id="preview">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('a[type="button"]').on('click', function(e) {
                e.preventDefault();
                var path = $(this).data('path');
                $('#preview').html('<iframe src="' + path + '" width="100%" height="500px"></iframe>');
            });
        });
    </script>
@endpush
