@extends('layouts.app')
@section('page-title', 'Service Details')
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Service Details</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <p class="form-control-plaintext">{{ $complaint->name }}</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Designation</label>
                    <p class="form-control-plaintext">{{ $complaint->designation }}</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <p class="form-control-plaintext">{{ $complaint->email }}</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone No.</label>
                    <p class="form-control-plaintext">{{ $complaint->phone }}</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label">PO. No.</label>
                    <p class="form-control-plaintext">{{ $complaint->po_number }}</p>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Photo / Video Uploaded</label>
                    @if($complaint->attachment)
                        @if(Str::endsWith($complaint->attachment, ['.mp4','.mov','.avi']))
                            <video src="{{ asset('storage/complaint/'.$complaint->attachment) }}" controls class="img-fluid rounded"></video>
                        @else
                            <img src="{{ asset('storage/complaint/'.$complaint->attachment) }}" alt="Uploaded Media" class="img-fluid rounded">
                        @endif
                    @else
                        <p class="form-control-plaintext">No file uploaded</p>
                    @endif
                </div>
            </div>

            {{-- Back Button --}}
            <div class="mt-4 text-end">
                <a href="{{ route('customer_service.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</section>

@endsection
