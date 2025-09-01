@extends('layouts.app')
@section('page-title', 'Register New Complaint')
@section('content')

    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <form action="{{ route('customer_service.store') }}" method="POST" class="row g-3"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="col-md-4">
                        <label class="form-label">Name</label>
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
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="col-md-4 ">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Site/Project Name</label>
                        <input type="text" name="site_project_name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">PO No.</label>
                        <input type="text" name="po_no" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Site Location</label>
                        <input type="text" name="site_location" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Upload Photo/Video</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Issue Faced</label>
                        <textarea name="issue_faced" rows="3" class="form-control" placeholder="Please write the issue faced..."></textarea>
                    </div>

                    {{-- Form Actions --}}
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Submit Complaint</button>
                        <a href="{{ route('customer_service.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
