@extends('layouts.app')
@section('page-title', 'Pricing Sheets')
@section('content')
    <div class="page-body">
        <div class="d-flex justify-content-end align-items-center">
            <a href="{{ asset('admin/googletool/integrate') }}" class="btn btn-secondary btn-sm" data-toggle="copy"
                data-copy-target="#google_drive_doc_modal" data-copy-value="{{ asset('admin/googletool/integrate') }}">
                Connect Google Account
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="bd-example">
                    <nav>
                        <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                aria-selected="true">Costing Sheet Pending</button>
                            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Costing
                                sheet submitted</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tender No</th>
                                            <th>Tender Name</th>
                                            <th>Due Date <br>Time</th>
                                            <th>EMD</th>
                                            <th>Tender<br>Value</th>
                                            <th>Final<br>Price</th>
                                            <th>Budget</th>
                                            <th>Gross<br>Margin</th>
                                            <th>Executive <br>Member</th>
                                            <th>Status</th>
                                            <th>Timer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingSheets as $tender)
                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                    Auth::user()->id == $tender->team_member ||
                                                    (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                <tr>
                                                    <td>{{ $tender->tender_no }}</td>
                                                    <td>{{ $tender->tender_name }}</td>
                                                    <td>
                                                        <span class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                        {{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                                        {{ date('h:i A', strtotime($tender->due_time)) }}
                                                    </td>
                                                    <td>{{ format_inr($tender->emd) }}</td>
                                                    <td>{{ format_inr($tender->gst_values) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->final_price) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->budget) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->gross_margin) }}</td>
                                                    <td>{{ $tender->users->name }}</td>
                                                    <td>{{ $tender->statuses->name }}</td>
                                                    <td>
                                                        @php
                                                            if ($tender) {
                                                                $timer = $tender->getTimer('costing_sheet');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $tender->remainedTime('costing_sheet');
                                                                }
                                                            }
                                                        @endphp
                                                        @if (isset($tender) && $timer)
                                                            <span class="d-none">{{ $remaining }}</span>
                                                            <span class="timer" id="timer-{{ $tender->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @elseif (isset($tender) && isset($remained))
                                                            <span class="d-none">0</span>
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($tender->sheet)
                                                            <a href="{{ $tender->sheet->driveid }}" target="_blank"
                                                                class="btn btn-primary btn-xs">Open</a>
                                                            <button type="button" class="btn btn-warning btn-xs"
                                                                data-id="{{ $tender->sheet->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#submit_sheet">
                                                                Submit
                                                            </button>
                                                            <!--<a href="" class="btn btn-info btn-xs">View</a>-->
                                                        @else
                                                            <form action="{{ asset('admin/googletoolssave') }}"
                                                                method="post" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="TenderInfo"
                                                                    value="{{ $tender->id }}">
                                                                <input type="hidden" name="title"
                                                                    value="{{ $tender->tender_name }}">
                                                                <button type="submit" class="btn btn-info btn-xs">
                                                                    Create Sheet
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tender No</th>
                                            <th>Tender Name</th>
                                            <th>Due Date <br>Time</th>
                                            <th>EMD</th>
                                            <th>Tender<br>Value</th>
                                            <th>Final<br>Price</th>
                                            <th>Budget</th>
                                            <th>Gross<br>Margin</th>
                                            <th>Executive <br>Member</th>
                                            <th>Status</th>
                                            <th>Timer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($submittedSheets as $tender)
                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                    Auth::user()->id == $tender->team_member ||
                                                    (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                <tr>
                                                    <td>{{ $tender->tender_no }}</td>
                                                    <td>{{ $tender->tender_name }}</td>
                                                    <td>
                                                        <span class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                        {{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                                        {{ date('h:i A', strtotime($tender->due_time)) }}
                                                    </td>
                                                    <td>{{ format_inr($tender->emd) }}</td>
                                                    <td>{{ format_inr($tender->gst_values) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->final_price) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->budget) }}</td>
                                                    <td>{{ format_inr($tender->sheet?->gross_margin) }}</td>
                                                    <td>{{ $tender->users->name }}</td>
                                                    <td>{{ $tender->statuses->name }}</td>
                                                    <td>
                                                        @php
                                                            if ($tender) {
                                                                $timer = $tender->getTimer('costing_sheet');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $tender->remainedTime('costing_sheet');
                                                                }
                                                            }
                                                        @endphp
                                                        @if (isset($tender) && $timer)
                                                            <span class="d-none">{{ $remaining }}</span>
                                                            <span class="timer" id="timer-{{ $tender->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @elseif (isset($tender) && isset($remained))
                                                            <span class="d-none">0</span>
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($tender->sheet)
                                                            <a href="{{ $tender->sheet->driveid }}" target="_blank"
                                                                class="btn btn-primary btn-xs">Open</a>
                                                            <button type="button" class="btn btn-warning btn-xs"
                                                                data-id="{{ $tender->sheet->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#submit_sheet">
                                                                Submit
                                                            </button>
                                                            <!--<a href="" class="btn btn-info btn-xs">View</a>-->
                                                        @else
                                                            <form action="{{ asset('admin/googletoolssave') }}"
                                                                method="post" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="TenderInfo"
                                                                    value="{{ $tender->id }}">
                                                                <input type="hidden" name="title"
                                                                    value="{{ $tender->tender_name }}">
                                                                <button type="submit" class="btn btn-info btn-xs">
                                                                    Create Sheet
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="submit_sheet" tabindex="-1" aria-labelledby="submit_sheetLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submit_sheetLabel">Submit Sheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('googletoolssubmitsheet') }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <input type="hidden" name="id" id="id">
                            <label for="final_price" class="form-label">Final Price (GST Inclusive)</label>
                            <input type="number" class="form-control" name="final_price" id="final_price"
                                min="0" step="any" required>
                        </div>
                        <div class="mb-3">
                            <label for="receipt" class="form-label">Receipt (Pre GST)</label>
                            <input type="number" class="form-control" name="receipt" id="receipt" min="0"
                                step="any" required>
                        </div>
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget (Pre GST)</label>
                            <input type="number" class="form-control" name="budget" id="budget" min="0"
                                step="any" required>
                        </div>
                        <div class="mb-3">
                            <label for="gross_margin" class="form-label">Gross Margin %age</label>
                            <input type="number" class="form-control" name="gross_margin" id="gross_margin"
                                min="0" max="100" step="any" readonly data-bs-toggle="tooltip"
                                title="(Receipt - Budget)/Receipt">
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#submit_sheet').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            $(this).find('input[name="id"]').val(id);

            $(this).find('#receipt, #budget').off('input').on('input', function() {
                let receipt = parseFloat($(this).closest('.modal').find('#receipt').val()) || 0;
                let budget = parseFloat($(this).closest('.modal').find('#budget').val()) || 0;
                let gross_margin = receipt > 0 ? ((receipt - budget) / receipt) * 100 : 0;
                $(this).closest('.modal').find('#gross_margin').val(gross_margin.toFixed(2));
            });
        });

        $('#submit_sheet').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
