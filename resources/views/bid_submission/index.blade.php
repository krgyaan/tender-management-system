@extends('layouts.app')
@section('page-title', 'Bid Submission')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Bid submission Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Bid submitted</button>
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
                                                    <th>Estimated Cost</th>
                                                    <th>Final Costing</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pendingTenders as $tdr)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tdr->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tdr->users->team))
                                                        <tr>
                                                            <td>{{ $tdr->tender_no }}</td>
                                                            <td>{{ $tdr->tender_name }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($tdr->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tdr->due_time)) }}<br>
                                                            </td>
                                                            <td>{{ format_inr($tdr->emd) }}</td>
                                                            <td>{{ format_inr($tdr->gst_values) }}</td>
                                                            <td>{{ format_inr($tdr->sheet?->final_price) }}</td>
                                                            <td>{{ optional($tdr->bs)->status ?? 'Submission Pending' }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $timer = $tdr->getTimer('bid_submission');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tdr->remainedTime(
                                                                            'bid_submission',
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
                                                                    href="{{ route('bs.show', $tdr->id) }}">View</a>
                                                                <button type="button" class="btn btn-xs btn-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#bidSubmittedModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Submit Bid
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#tenderMissedModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Tender Missed
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
                                                    <th>Estimated Cost</th>
                                                    <th>Final Costing</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($submittedTenders as $tdr)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tdr->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tdr->users->team))
                                                        <tr>
                                                            <td>{{ $tdr->tender_no }}</td>
                                                            <td>{{ $tdr->tender_name }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($tdr->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tdr->due_time)) }}<br>
                                                            </td>
                                                            <td>{{ format_inr($tdr->emd) }}</td>
                                                            <td>{{ format_inr($tdr->gst_values) }}</td>
                                                            <td>{{ format_inr($tdr->sheet?->final_price) }}</td>
                                                            <td>{{ optional($tdr->bs)->status ?? 'Submission Pending' }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $timer = $tdr->getTimer('bid_submission');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tdr->remainedTime(
                                                                            'bid_submission',
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
                                                                    href="{{ route('bs.show', $tdr->id) }}">View</a>
                                                                <button type="button" class="btn btn-xs btn-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#bidSubmittedModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Submit Bid
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#tenderMissedModal"
                                                                    data-tender-id="{{ $tdr->id }}">
                                                                    Tender Missed
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

    <!-- Bid Submitted Modal -->
    <div class="modal fade" id="bidSubmittedModal" tabindex="-1" role="dialog" aria-labelledby="bidSubmittedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bidSubmittedModalLabel">Bid Submission Details</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="bidSubmittedForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tender_id" id="tenderId">
                        <div class="form-group">
                            <label>Bid Submission Date and Time</label>
                            <input type="datetime-local" class="form-control" name="submission_datetime" required>
                        </div>
                        <div class="form-group">
                            <label>Upload Bid Documents</label>
                            <input type="file" class="form-control" name="bid_documents">
                        </div>
                        <div class="form-group">
                            <label>Upload Proof of Submission</label>
                            <input type="file" class="form-control" name="submission_proof" required>
                        </div>
                        <div class="form-group">
                            <label>Upload Final Bidding Price</label>
                            <input type="file" class="form-control" name="final_price" required>
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

    <!-- Tender Missed Modal -->
    <div class="modal fade" id="tenderMissedModal" tabindex="-1" role="dialog"
        aria-labelledby="tenderMissedModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderMissedModalLabel">Tender Missed Report</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="tenderMissedForm" method="POST">
                        @csrf
                        <input type="hidden" name="tender_id" id="missedTenderId">
                        <div class="form-group">
                            <label>Reason for missing the tender</label>
                            <textarea class="form-control" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>What would you do to ensure this is not repeated?</label>
                            <textarea class="form-control" name="prevention_steps" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Any improvements needed in the TMS system?</label>
                            <textarea class="form-control" name="system_improvements" rows="3" required></textarea>
                            <small class="text-muted">to help you avoid making same mistake again</small>
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
        $(document).ready(function() {
            $('#bidSubmittedModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                $('#tenderId').val(tenderId);
                $('#bidSubmittedForm').attr('action', '/bid-submission/submit-bid/' + tenderId);
            });

            $('#tenderMissedModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender-id');
                $('#missedTenderId').val(tenderId);
                $('#tenderMissedForm').attr('action', '/bid-submission/mark-missed/' + tenderId);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
