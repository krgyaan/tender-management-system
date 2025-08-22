@extends('layouts.app')
@section('page-title', 'New Enquiry Received')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')

                <form method="POST" action="{{ route('enquiries.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if ($lead)
                        <p class="text-success text-center pb-3">
                            Congratulations on receiving a new enquiry from <i>{{ $lead->company_name }}
                                ({{ $lead->state }}, {{ $lead->country }})</i>.
                        </p>
                    @endif
                    <div class="row mb-3">
                        <input type="hidden" name="lead_id" value="{{ $lead ? $lead->id : '' }}">
                        <div class="form-group col-md-12 mb-3">
                            <label for="organisation" class="form-label">Enquiry Name</label>
                            <input type="text" class="form-control" id="enquiryName" name="enq_name" required readonly
                                value="{{ old('enq_name') }}">
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label for="organisation" class="form-label">Organisation (End user)</label>
                            <select class="form-control" id="organisation" name="organisation" required>
                                <option value="">Select Organisation</option>
                                @foreach ($organisations as $organisation)
                                    <option value="{{ $organisation->id }}"
                                        {{ old('organisation') == $organisation->id ? 'selected' : '' }}>
                                        {{ $organisation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label for="item" class="form-label">Item</label>
                            <select class="form-control" id="item" name="item" required>
                                <option value="">Select Item</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <select class="form-control" id="location" name="location" required>
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->acronym }}" {{ !$location->acronym ? 'disabled' : '' }}
                                        {{ old('location') == $location->acronym ? 'selected' : '' }}>
                                        {{ $location->address }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label for="approx_value" class="form-label">Approx Value (₹)</label>
                            <input type="number" step="any" min="0" class="form-control" id="approx_value"
                                name="approx_value" required value="{{ old('approx_value') }}">
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label class="form-label">Site Visit Required</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="site_visit_required"
                                    id="site_visit_yes" value="Y" required
                                    {{ old('site_visit_required') == 'Y' ? 'checked' : '' }}>
                                <label class="form-check-label" for="site_visit_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="site_visit_required" id="site_visit_no"
                                    value="N" {{ old('site_visit_required') == 'N' ? 'checked' : '' }}>
                                <label class="form-check-label" for="site_visit_no">No</label>
                            </div>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label for="enquiry_file" class="form-label">Upload Enquiry Document</label>
                            <input type="file" class="form-control" id="enquiry_file" name="enquiry_file"
                                accept=".pdf,.doc,.docx,.jpg,.png" value="{{ old('enquiry_file') }}">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Submit Enquiry</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Enquiry Name: Organization+Item+Location
            $("#organisation, #item, #location").on('change', function() {
                var org = $("#organisation option:selected").text();
                var item = $("#item option:selected").text();
                var location = $("#location option:selected").val();
                $("#enquiryName").val(org.trim() + ' ' + item.trim() + ' ' + location.trim());
            })
        })
    </script>
@endpush
