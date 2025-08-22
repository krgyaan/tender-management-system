@extends('layouts.app')
@section('page-title', 'Add GST R1')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add GST R1</h1>
        </div>
        <div class="section-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('gstr1.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="gst_r1_sheet">Upload GST R1 Sheet</label>
                            <input type="file" name="gst_r1_sheet" id="gst_r1_sheet" class="form-control" required>
                            <small class="form-text text-muted">Accepted: XLS, XLSX, PDF (Max 5MB)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tally_data_link">Tally Data (Google Drive Link)</label>
                            <input type="url" name="tally_data_link" id="tally_data_link" class="form-control"
                                placeholder="https://drive.google.com/..." value="{{ old('tally_data_link') }}" required>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="confirmation" id="confirmation" class="form-check-input"
                                value="1" {{ old('confirmation') ? 'checked' : '' }} required>
                            <label for="confirmation" class="form-check-label">GST R1 Confirmation</label>
                        </div>

                        <div class="form-group mb-3">
                            <label for="return_file">Upload GST R1 Return (Optional)</label>
                            <input type="file" name="return_file" id="return_file" class="form-control">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
