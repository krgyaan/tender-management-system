@extends('layouts.app')
@section('page-title', 'OEM Performance')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="new-user-info">
                    <form method="POST" action="">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label" for="oem">Select OEM:</label>
                                <select name="oem" class="form-control select2" id="oem" required>
                                    <option value="">Select OEM</option>
                                    @foreach ($oems as $oem)
                                        <option value="{{ $oem->id }}"
                                            {{ old('oem', $_POST['oem'] ?? '') == $oem->id ? 'selected' : '' }}>
                                            {{ $oem->name }}
                                        </option>
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
                        <div class="text-end">
                            <button type="submit" id="submit" name="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
                <hr class="m-0 mt-3 p-0">
            </div>
            @if ($result)
                <div id="result" class="pb-3">
                    <h4 class="text-center">Tenders Not Allowed by This OEM</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Team Member</th>
                                    <th>Team</th>
                                    <th>Tender</th>
                                    <th>GST Value</th>
                                    <th>Due Date Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notAllowedTenders as $tender)
                                    <tr>
                                        <td>{{ $tender['team'] }}</td>
                                        <td>{{ $tender['member'] }}</td>
                                        <td>
                                            <b>
                                                {{ $tender['tender_name'] }}
                                            </b><br>
                                            {{ $tender['tender_no'] }}
                                        </td>
                                        <td>{{ format_inr($tender['gst_values']) }}</td>
                                        <td>{{ $tender['due_date'] }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <h4 class="text-center mt-3">RFQs Sent to This OEM</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Team Member</th>
                                    <th>Tender</th>
                                    <th>GST Value</th>
                                    <th>Due Date Time</th>
                                    <th>RFQ Sent on</th>
                                    <th>Response get on</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rfqsSentToOem as $tender)
                                    <tr>
                                        <td>
                                            <b>{{ $tender['member'] }}</b>
                                            <br>
                                            <span class="text-muted">
                                                of {{ $tender['team'] }} Team
                                            </span>
                                        </td>
                                        <td>
                                            <b>
                                                {{ $tender['tender_name'] }}
                                            </b><br>
                                            {{ $tender['tender_no'] }}
                                        </td>
                                        <td>{{ format_inr($tender['gst_values']) }}</td>
                                        <td>{{ $tender['due_date'] }}</td>
                                        <td>{{ $tender['rfq_sent_on'] }}</td>
                                        <td>{{ $tender['rfq_response'] }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h4 class="text-center mt-3">Worked With This OEM</h4>
                    <div class="table-responsive">
                        <table class="table-bordered w-100 summary">
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
                                        <td class="text-wrap">
                                            @foreach ($value['tender'] as $tender)
                                                <small class="badge bg-success">
                                                    {{ $tender }},
                                                </small>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#oem').select2({
                placeholder: 'Select OEM',
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
            display: flex;
            align-items: center;
            line-height: 2.3;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            top: 0;
            right: 10px;
        }

        .summary th {
            font-weight: bold;
        }

        .summary th,
        .summary td {
            padding: 8px;
            font-size: 16px;
        }
    </style>
@endpush
