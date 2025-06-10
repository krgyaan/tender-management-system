@extends('layouts.app')
@section('page-title', 'Add Basic Details')
@section('content')
    <section>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-4">
                    <form method="post" action="{{ asset('/admin/basicdetailaddpost') }}" enctype="multipart/form-data"
                        id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <div class="col-md-4">
                            <label for="name_id" class="form-label">Tender Name</label>
                            <input type="hidden" name="tender_name_id" id="name_id" value="{{ $tender->id }}" required>
                            <input type="text" class="form-control" name="tender_name" id="name"
                                value="{{ $tender->tender_name }}" required readonly>
                            @error('tender_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="number" class="form-label">WO Number</label>
                            <input type="text" class="form-control" name="number" id="number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">WO Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pre_gst" class="form-label">WO Value (Pre-GST)</label>
                            <input type="text" class="form-control" name="pre_gst" id="pre_gst" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pre_amt" class="form-label">WO Value (GST Amt.)</label>
                            <input type="text" class="form-control" name="pre_amt" id="pre_amt" required>
                        </div>
                        <div class="col-md-4">
                            <label for="image" class="form-label">LOA/GEM PO/LOI/Draft WO</label>
                            <input type="file" class="form-control" name="image" id="image" required>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
