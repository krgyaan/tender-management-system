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
                        <button id="printAllProofs" class="btn btn-outline-primary btn-sm">
                            Print All Proofs
                        </button>
                    </div>
                    <div class="card">
                        @include('partials.messages')
                        <div class="card-body">
                            <h5 class="card-title">Proof</h5>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                @php
                                    $string = Crypt::decrypt($proof);
                                    $allProofs = [];
                                    if ($string) {
                                        $proofs = str_replace('],[', ',', $string);
                                        $clean = trim($proofs, '[]');
                                        $array = explode(',', $clean);
                                        $pdf = array_map(function ($item) {
                                            return trim($item, '"');
                                        }, $array);
                                        foreach ($pdf as $key => $file) {
                                            $path = asset('uploads/employeeimprest/' . $file);
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            $allProofs[] = [
                                                'path' => $path,
                                                'ext' => $ext,
                                                'file' => $file,
                                            ];
                                            if ($ext == 'pdf') {
                                                echo '<a class="btn btn-xs btn-outline-light" type="button" data-path="' .
                                                    $path .
                                                    '">PDF-' .
                                                    ($key + 1) .
                                                    '</a>';
                                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                echo '<a class="btn btn-xs btn-outline-light" type="button" data-path="' .
                                                    $path .
                                                    '">Image-' .
                                                    ($key + 1) .
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
                            <script>
                                // Pass PHP array to JS
                                window.allProofs = @json($allProofs ?? []);
                            </script>
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
            
            $('#printAllProofs').on('click', function() {
                var proofs = window.allProofs || [];
                var html = '';
                proofs.forEach(function(proof) {
                    if (proof.ext === 'pdf') {
                        html +=
                            '<div class="pdf-notice" style="margin-bottom:20px;padding:10px;border:1px solid #ccc;">PDF file: <a href="' +
                            proof.path + '" target="_blank">' + proof.file +
                            '</a><br><small style="color:red;">PDFs cannot be printed together. Please open and print this PDF separately.</small></div>';
                    } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(proof.ext)) {
                        html += '<img src="' + proof.path +
                            '" alt="" />';
                    } else {
                        html += '<p>Unknown file type - ' + proof.file + '</p>';
                    }
                });
                $('#preview').html(html);
                setTimeout(function() {
                    window.print();
                }, 500); // Wait for rendering
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #preview,
            #preview * {
                visibility: visible !important;
            }
            #preview {
                position: absolute;
                left: 0;
                top: 0;
                width: 100vw;
                background: #fff;
                z-index: 9999;
                padding: 0;
                margin: 0;
            }
            /* Hide PDF notices in print */
            #preview .pdf-notice {
                display: none !important;
            }
            #preview img {
                display: block;
                margin: 0 auto;
                max-width: 100vw;
                max-height: 95vh;
                width: auto;
                height: auto;
                page-break-before: always;
                page-break-after: always;
                break-inside: avoid;
                object-fit: contain;
            }
            #preview img:first-child {
                page-break-before: auto;
            }
        }
    </style>
@endpush