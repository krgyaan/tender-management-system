@extends('layouts.app')
@section('page-title', 'Customer Performance')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="new-user-info">
                    <form method="POST" action="">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="form-group col-md-3">
                                <label class="form-label" for="organization">Organization:</label>
                                <select name="organization" class="form-control select2" id="organization" required>
                                    <option value="">Select Organization</option>
                                    @foreach ($orgs as $org)
                                        <option value="{{ $org->id }}"
                                            {{ old('organization', $_POST['organization'] ?? '') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="form-label" for="team_type">Team:</label>
                                <select name="team_type" class="form-control" id="team_type">
                                    <option value="">Select Team Type</option>
                                    <option {{ old('team_type', $_POST['team_type'] ?? '') == 'AC' ? 'selected' : '' }}
                                        value="AC">AC
                                    </option>
                                    <option {{ old('team_type', $_POST['team_type'] ?? '') == 'DC' ? 'selected' : '' }}
                                        value="DC">DC
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="form-label" for="item_heading">Item Heading:</label>
                                <select name="item_heading" class="form-control" id="item_heading">
                                    <option value="">Select Item Heading</option>
                                    {{ $headings }}
                                    @foreach ($headings as $heading)
                                        <option
                                            {{ old('item_heading', $_POST['heading'] ?? '') == $heading->id ? 'selected' : '' }}
                                            value="{{ $heading->id }}">{{ $heading->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="from_date">From Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="from_date" class="form-control" id="from_date"
                                            value="{{ old('from_date') ?? ($_POST['from_date'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="to_date">To Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="to_date" class="form-control" id="to_date"
                                            value="{{ old('to_date') ?? ($_POST['to_date'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="submit" name="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if ($result)
            <div class="row">
                @foreach ($summary as $name => $value)
                    <div class="col-lg-3">
                        <div class="card shining-card">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">
                                    {{ Str::title(str_replace('_', ' ', $name)) }}
                                </a>
                                <div class="progress-detail pt-3">
                                    <h5 class="counter text-success" style="visibility: visible;">Count:
                                        {{ $value['count'] }}
                                    </h5>
                                    <h4 class="counter text-success" style="visibility: visible;">
                                        ₹{{ format_inr($value['value']) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Details Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Tender Summary Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="w-100 table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Value</th>
                                    <th>Tenders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary as $name => $value)
                                    <tr>
                                        <td>{{ Str::title(str_replace('_', ' ', $name)) }}</td>
                                        <td>{{ $value['count'] }}</td>
                                        <td>₹{{ format_inr($value['value']) }}</td>
                                        <td>
                                            @foreach ($value['tender'] as $tender)
                                                <small class="badge bg-success">
                                                    {{ $tender }}
                                                </small>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#organization').select2({
                placeholder: 'Select Organization',
                allowClear: true,
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 0 0.75rem;
            border: 1px solid #000;
            border-radius: 0.375rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.3;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            top: 0;
            right: 10px;
        }

        th {
            font-weight: bold;
        }
        th, td {
            padding: 8px;
            font-size: 16px;
        }
    </style>
@endpush
