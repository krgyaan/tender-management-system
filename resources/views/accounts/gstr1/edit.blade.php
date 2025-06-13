@extends('layouts.app')
@section('page-title', 'Edit GST R1 Upload')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Edit GST R1 Upload</h5>
                    </div>
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
                                                <li>Update the GST R1 Sheet in XLS, XLSX or PDF format</li>
                                                <li>Update the Tally Data Google Drive link if needed</li>
                                                <li>Confirm the submission by checking the confirmation box</li>
                                                <li>Optionally update the GST R1 Return file</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('gst-r1.update', $gstR1->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="gst_r1_sheet" class="form-label">Update GST R1 Sheet</label>
                                    <input type="file" class="form-control" id="gst_r1_sheet" name="gst_r1_sheet">
                                    <div class="form-text">Accepted formats: XLS, XLSX, PDF (Max 5MB)</div>
                                    @if ($gstR1->gst_r1_sheet_path)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $gstR1->gst_r1_sheet_path) }}"
                                                class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i> Download Current File
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="tally_data_link" class="form-label">Tally Data (Google Drive Link)</label>
                                    <input type="url" class="form-control" id="tally_data_link" name="tally_data_link"
                                        value="{{ old('tally_data_link', $gstR1->tally_data_link) }}"
                                        placeholder="https://drive.google.com/..." required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirmation"
                                            name="confirmation" value="1" required
                                            {{ old('confirmation', $gstR1->confirmation) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="confirmation">
                                            GST R1 Confirmation
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="return_file" class="form-label">Update GST R1 Return (Optional)</label>
                                    <input type="file" class="form-control" id="return_file" name="return_file">
                                    @if ($gstR1->return_file_path)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $gstR1->return_file_path) }}"
                                                class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i> Download Current Return
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('gst-r1.show', $gstR1->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update
                                    </button>
                                </div>
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

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0 !important;
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
