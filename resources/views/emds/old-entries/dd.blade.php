@extends('layouts.app')
@section('page-title', 'Old DD Entry')

@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('dd-old-entry') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="project_name">Project Name</label>
                                    @php
                                        $projects = \App\Models\Project::latest()->get()->pluck('project_name');
                                    @endphp
                                    <select class="form-control" name="project_name" id="project_name">
                                        <option value="">-- Select Project --</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project }}">
                                                {{ $project }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="dd_favour">DD in Favour of</label>
                                    <input type="text" name="dd_favour" id="dd_favour"
                                        class="form-control @error('dd_favour') is-invalid @enderror" required>
                                    @error('dd_favour')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="dd_payable">DD Payable at</label>
                                    <input type="text" name="dd_payable" id="dd_payable"
                                        class="form-control @error('dd_payable') is-invalid @enderror" required>
                                    @error('dd_payable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="dd_date">DD Date</label>
                                    <input type="date" name="dd_date" id="dd_date"
                                        class="form-control @error('dd_date') is-invalid @enderror" required>
                                    @error('dd_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="dd_amount">Amount</label>
                                    <input type="number" name="dd_amount" id="dd_amount"
                                        class="form-control @error('dd_amount') is-invalid @enderror" step="0.01"
                                        min="0" required>
                                    @error('dd_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="dd_purpose">Purpose of DD</label>
                                    <select name="dd_purpose" id="dd_purpose"
                                        class="form-control @error('dd_purpose') is-invalid @enderror" required>
                                        <option value="">Select Purpose</option>
                                        <option value="EMD">EMD</option>
                                        <option value="Tender Fees">Tender Fees</option>
                                        <option value="Security Deposit">Security Deposit</option>
                                        <option value="Other Payment">Other Payment</option>
                                        <option value="Other Security">Other Security</option>
                                    </select>
                                    @error('dd_purpose')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 form-group mb-3">
                                    <label class="form-label" for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror"></textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#project_name').select2({
                placeholder: 'Select Project',
                allowClear: true,
                width: '100%',
                height: 38,
            });
        });
    </script>
@endpush
