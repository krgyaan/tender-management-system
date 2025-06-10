@extends('layouts.app')
@section('page-title', 'Costing Approval')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Costing Approved</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Reject/Redo Costing</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender Due <br> Date and Time</th>
                                                    <th>EMD</th>
                                                    <th>Estimated<br> Cost</th>
                                                    <th>Final<br> Costing</th>
                                                    <th>Budget</th>
                                                    <th>Gross<br> Margin %</th>
                                                    <th>Team <br> Executive</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pendingTenders as $tdr)
                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                            Auth::user()->id == $tdr->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tdr->users->team))
                                                        <tr>
                                                            <td>{{ $tdr->tender_no }}</td>
                                                            <td>{{ $tdr->tender_name }}</td>
                                                            <td>
                                                                <span class="d-none">{{ strtotime($tdr->due_date) }}</span>
                                                                {{ date('d-m-Y', strtotime($tdr->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tdr->due_time)) }}<br>
                                                            </td>
                                                            <td>{{ format_inr($tdr->emd) }}</td>
                                                            <td>{{ format_inr($tdr->gst_values) }}</td>
                                                            <td>{{ format_inr($tdr->sheet->final_costing ?? '') }}</td>
                                                            <td>{{ format_inr($tdr->sheet->budget ?? '') }}</td>
                                                            <td>{{ $tdr->sheet->gross_margin ?? 'NA' }} %</td>
                                                            <td>{{ $tdr->users->name }}</td>
                                                            <td>{{ $tdr->statuses->name }}</td>
                                                            <td>
                                                                @php
                                                                    $timer = $tdr->getTimer('costing_sheet_approval');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tdr->remainedTime(
                                                                            'costing_sheet_approval',
                                                                        );
                                                                    }
                                                                @endphp
                                                                @if ($timer)
                                                                    {{-- Sortable timer --}}
                                                                    <span class="d-none">{{ $remaining }}</span>
                                                                    <span class="timer" id="timer-{{ $tdr->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    <span class="d-none">0</span>
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-xs btn-info"
                                                                    href="{{ route('costing-approval.show', $tdr->id) }}">
                                                                    View
                                                                </a>
                                                                <a href="{{ $tdr->sheet->driveid }}" target="_blank"
                                                                    class="btn btn-xs btn-primary">
                                                                    Edit Sheet
                                                                </a>
                                                                <button type="button" class="btn btn-xs btn-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approveSheetModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Approve Sheet
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender Due <br> Date and Time</th>
                                                    <th>EMD</th>
                                                    <th>Estimated<br> Cost</th>
                                                    <th>Final<br> Costing</th>
                                                    <th>Budget</th>
                                                    <th>Gross<br> Margin %</th>
                                                    <th>Team <br> Executive</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($approvedTenders as $tdr)
                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                            Auth::user()->id == $tdr->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tdr->users->team))
                                                        <tr>
                                                            <td>{{ $tdr->tender_no }}</td>
                                                            <td>{{ $tdr->tender_name }}</td>
                                                            <td>
                                                                <span class="d-none">{{ strtotime($tdr->due_date) }}</span>
                                                                {{ date('d-m-Y', strtotime($tdr->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tdr->due_time)) }}<br>
                                                            </td>
                                                            <td>{{ format_inr($tdr->emd) }}</td>
                                                            <td>{{ format_inr($tdr->gst_values) }}</td>
                                                            <td>{{ format_inr($tdr->sheet->final_costing ?? '') }}</td>
                                                            <td>{{ format_inr($tdr->sheet->budget ?? '') }}</td>
                                                            <td>{{ $tdr->sheet->gross_margin ?? 'NA' }} %</td>
                                                            <td>{{ $tdr->users->name }}</td>
                                                            <td>{{ $tdr->statuses->name }}</td>
                                                            <td>
                                                                @php
                                                                    $timer = $tdr->getTimer('costing_sheet_approval');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tdr->remainedTime(
                                                                            'costing_sheet_approval',
                                                                        );
                                                                    }
                                                                @endphp
                                                                @if ($timer)
                                                                    {{-- Sortable timer --}}
                                                                    <span class="d-none">{{ $remaining }}</span>
                                                                    <span class="timer" id="timer-{{ $tdr->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    <span class="d-none">0</span>
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-xs btn-info"
                                                                    href="{{ route('costing-approval.show', $tdr->id) }}">
                                                                    View
                                                                </a>
                                                                <a href="{{ $tdr->sheet->driveid }}" target="_blank"
                                                                    class="btn btn-xs btn-primary">
                                                                    Edit Sheet
                                                                </a>
                                                                <button type="button" class="btn btn-xs btn-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#approveSheetModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Approve Sheet
                                                                </button>
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
        </div>
    </section>

    {{-- Modal --}}
    <div class="modal fade" id="approveSheetModal" tabindex="-1" aria-labelledby="approveSheetModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveSheetModalLabel">Approve Costing Sheet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="costingStatus" class="form-label">Costing Sheet</label>
                            <select class="form-select" id="costingStatus" name="costing_status" required>
                                <option value="">Select Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected/Redo">Rejected/Redo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="costingRemarks" class="form-label">Costing Remarks (if any)</label>
                            <textarea class="form-control" id="costingRemarks" name="costing_remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#approveSheetModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                var form = $(this).find('form');
                form.attr('action', '/costing-approval/approve-sheet/' + tenderId);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
