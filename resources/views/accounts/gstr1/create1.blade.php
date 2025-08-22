@extends('layouts.app')
@section('page-title', 'GST R1 Upload')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="bd-example pb-3">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            GST R1 Upload Instructions
                                        </button>
                                    </h4>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <ol>
                                                <li>Upload the GST R1 Sheet in XLS, XLSX or PDF format</li>
                                                <li>Provide the Tally Data Google Drive link</li>
                                                <li>Confirm the submission by checking the confirmation box</li>
                                                <li>Optionally upload the GST R1 Return file</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="gst_r1_sheet" class="form-label">Upload GST R1 Sheet</label>
                                    <input type="file" class="form-control" id="gst_r1_sheet" name="gst_r1_sheet"
                                        required>
                                    <div class="form-text">Accepted formats: XLS, XLSX, PDF (Max 5MB)</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="tally_data_link" class="form-label">Tally Data (Google Drive Link)</label>
                                    <input type="url" class="form-control" id="tally_data_link" name="tally_data_link"
                                        placeholder="https://drive.google.com/..." required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirmation"
                                            name="confirmation" value="1" required>
                                        <label class="form-check-label" for="confirmation">
                                            GST R1 Confirmation
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="return_file" class="form-label">GST R1 Return (Optional)</label>
                                    <input type="file" class="form-control" id="return_file" name="return_file">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="reset" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }

        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .card {
            border-radius: 0.5rem;
        }

        .form-label {
            font-weight: 500;
        }

        input[type="file"]:hover {
            background-color: #f8f9fa !important;
            outline: 1px solid #86b7fe !important;
        }
    </style>
@endpush
