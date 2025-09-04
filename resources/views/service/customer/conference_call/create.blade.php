@extends('layouts.app')
@section('page-title', 'Customer Conference Call')
@section('content')

    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <form action="{{ route('customer_service.conference_call.store') }}" method="POST" class="row g-3"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value={{ $complaint->id }} name="complaint_id" />

                    <div class="col-md-12">
                        <label class="form-label">Issue Description in Detail</label>
                        <textarea name="issue_description" rows="4" class="form-control" placeholder="Describe the issue in detail..."
                            required></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Material/Tools Required for Resolution</label>
                        <textarea name="materials_required" rows="3" class="form-control"
                            placeholder="List the materials/tools required..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Actions Planned for Resolution</label>
                        <textarea name="actions_planned" rows="3" class="form-control"
                            placeholder="Write down the planned actions for resolution..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Additional Photos/Videos</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                        <small class="text-muted">You can upload multiple files.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Voice Recording</label>
                        <input type="file" name="voice_recording" class="form-control" accept="audio/*">
                    </div>

                    {{-- Form Actions --}}
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Submit Conference Call</button>
                        <a href="{{ route('customer_service.conference_call.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
