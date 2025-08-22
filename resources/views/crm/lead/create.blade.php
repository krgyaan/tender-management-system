@extends('layouts.app')
@section('page-title', 'Create New Lead')
@section('content')
    @php
        $countries = [
            (object) ['name' => 'India'],
            (object) ['name' => 'Nepal'],
            (object) ['name' => 'Sri Lanka'],
            (object) ['name' => 'UAE'],
            (object) ['name' => 'United States'],
            (object) ['name' => 'United Kingdom'],
        ];
    @endphp
    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <form action="{{ route('lead.store') }}" method="POST" class="row g-3">
                    @csrf

                    {{-- Basic Information Section --}}
                    @include('partials.div-separator', ['text' => 'Basic Information'])

                    <div class="col-md-4">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Person Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>

                    @include('partials.div-separator', ['text' => 'Location Details'])

                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <select name="country" class="form-select" id="country-select" required>
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->name }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4" id="state-dropdown-container">
                        <label class="form-label">State</label>
                        <select name="state" class="form-select" id="state-dropdown" required>
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->name }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-none" id="state-text-container">
                        <label class="form-label">State</label>
                        <input type="text" name="state_text" class="form-control" id="state-text"
                            placeholder="Enter State Name" />
                    </div>

                    {{-- Lead Type Section --}}
                    @include('partials.div-separator', ['text' => 'Lead Details'])

                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Industry</label>
                        <select name="industry" class="form-select" required>
                            <option value="">Select Industry</option>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->name }}">{{ $industry->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Team</label>
                        <select name="team" class="form-select" required>
                            <option value="">Select Team</option>
                            @foreach (['AC', 'DC', 'IB'] as $team)
                                <option value="{{ $team }}">{{ $team }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Discussion Section --}}
                    @include('partials.div-separator', ['text' => 'Additional Details'])
                    <div class="col-md-12">
                        <label class="form-label">Points Discussed</label>
                        <textarea name="points_discussed" class="form-control" rows="3" placeholder="Enter discussion points..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">VE Responsibility</label>
                        <textarea name="ve_responsibility" class="form-control" rows="3" placeholder="Enter VE responsibilities..."></textarea>
                    </div>

                    {{-- Form Actions --}}
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Submit Lead</button>
                        <a href="{{ route('lead.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            const $countrySelect = $('#country-select');
            const $stateDropdownContainer = $('#state-dropdown-container');
            const $stateTextContainer = $('#state-text-container');
            const $stateDropdown = $('#state-dropdown');
            const $stateText = $('#state-text');

            $countrySelect.on('change', function() {
                if ($(this).val() === 'India') {
                    $stateDropdownContainer.removeClass('d-none');
                    $stateDropdown.prop('required', true);
                    $stateTextContainer.addClass('d-none');
                    $stateText.prop('required', false);
                } else {
                    $stateDropdownContainer.addClass('d-none');
                    $stateDropdown.prop('required', false);
                    $stateTextContainer.removeClass('d-none');
                    $stateText.prop('required', true);
                }
            });
        });
    </script>
@endpush
