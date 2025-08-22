@extends('layouts.app')
@section('page-title', 'Followups Dashboard')
@php
    use App\Models\User;

    $ferq = [
        '1' => 'Daily',
        '2' => 'Alternate Days',
        '3' => '2 times a day',
        '4' => 'Weekly (every Mon)',
        '5' => 'Twice a Week (every Mon & Thu)',
        '6' => 'Stop',
    ];
    $stop = [
        '1' => 'The person is getting angry/or has requested to stop',
        '2' => 'Followup Objective achieved',
        '3' => 'External Followup Initiated',
        '4' => 'Remarks',
    ];
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('followups.create') }}" class="btn btn-primary btn-sm">Assign Followup</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="bd-example pb-3">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            View Followup Amount and Released Amount
                                        </button>
                                    </h4>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-center w-100">
                                                <div class="table-responsive">
                                                    <table class="table-bordered table-info">
                                                        <thead class="bg-secondary">
                                                            <th>Name</th>
                                                            <th>Target Amount</th>
                                                            <th>Released Amount</th>
                                                            <th>Pending Amount</th>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $total_target = 0;
                                                                $total_achieved = 0;
                                                            @endphp
                                                            @foreach ($amount as $key => $value)
                                                                <tr>
                                                                    <td>{{ User::find($key)->name }}</td>
                                                                    <td>{{ format_inr($value['total_amount']) }}</td>
                                                                    <td>{{ format_inr($value['achieved_amount']) }}</td>
                                                                    <td>{{ format_inr($value['total_amount'] - $value['achieved_amount']) }}</td>
                                                                </tr>
                                                                @php
                                                                    $total_target += $value['total_amount'];
                                                                    $total_achieved += $value['achieved_amount'];
                                                                @endphp
                                                            @endforeach
                                                            @if (in_array(auth()->user()->role, ['admin']))
                                                                <tr class="bg-info">
                                                                    <td>Total</td>
                                                                    <td>{{ format_inr($total_target) }}</td>
                                                                    <td>{{ format_inr($total_achieved) }}</td>
                                                                    <td>{{ format_inr($total_target - $total_achieved) }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link btn btn-white active" id="nav-onging-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-onging" type="button" role="tab"
                                        aria-controls="nav-onging" aria-selected="true">Ongoing</button>
                                    <button class="nav-link btn btn-white" id="nav-achieved-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-achieved" type="button" role="tab"
                                        aria-controls="nav-achieved" aria-selected="false">Achieved</button>
                                    <button class="nav-link btn btn-white" id="nav-angry-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-angry" type="button" role="tab" aria-controls="nav-angry"
                                        aria-selected="false">Angry/External</button>
                                    <button class="nav-link btn btn-white" id="nav-future-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-future" type="button" role="tab"
                                        aria-controls="nav-future" aria-selected="false">Future FollowUp</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-onging" role="tabpanel"
                                    aria-labelledby="nav-onging-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="fdr">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Area</th>
                                                    <th>Organisation <br>Name</th>
                                                    <th>Concerned <br>Person</th>
                                                    <th>FollowUp <br>For</th>
                                                    <th>Amount</th>
                                                    <th>Frequency</th>
                                                    <th>Status</th>
                                                    <th>Last Status<br>Update</th>
                                                    <th>Followup <br>Since</th>
                                                    <th>Assigned by</th>
                                                    <th>Assigned to</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($ongoing->count() > 0)
                                                    @foreach ($ongoing as $followup)
                                                        @if (
                                                            $followup->created_by == Auth::user()->id ||
                                                                $followup->assigned_to == Auth::user()->id ||
                                                                in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $followup->area }}</td>
                                                                <td>{{ $followup->party_name }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-info"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#followPerson"
                                                                        data-id="{{ $followup->followPerson }}">
                                                                        See Person
                                                                    </button>
                                                                </td>

                                                                <td>{{ $followup->followup_for }}</td>
                                                                <td>{{ $followup->amount }}</td>
                                                                <td>
                                                                    {{ $followup->frequency ? $ferq[$followup->frequency] : '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->assign_initiate }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->latest_comment }}
                                                                    <br>
                                                                    <span class="text-muted text-success">
                                                                        on
                                                                        {{ date('d M y', strtotime($followup->updated_at)) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->format('d-m-Y') }}
                                                                    <br>
                                                                    {{ floor(\Carbon\Carbon::parse($followup->start_from)->diffInDays(now())) }}
                                                                    Days,
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->diffInHours(now()) % 24 }}
                                                                    Hrs
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->creator)->name ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->assignee)->name ?? '' }}
                                                                </td>
                                                                <td class="d-flex gap-2 flex-wrap">
                                                                    <a href="{{ route('followups.edit', $followup->id) }}"
                                                                        class="btn btn-xs btn-info">
                                                                        Initiate Auto Followup
                                                                    </a>
                                                                    <a type="button" class="btn btn-xs btn-primary"
                                                                        data-id="{{ $followup->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#updateFollowup">
                                                                        Update Status
                                                                    </a>
                                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                        <form
                                                                            action="{{ route('followups.destroy', $followup->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-xs btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this followup?')">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-achieved" role="tabpanel"
                                    aria-labelledby="nav-achieved-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="fdr">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Area</th>
                                                    <th>Organisation <br>Name</th>
                                                    <th>Concerned <br>Person</th>
                                                    <th>FollowUp <br>For</th>
                                                    <th>Amount</th>
                                                    <th>Frequency</th>
                                                    <th>Status</th>
                                                    <th>Last Status<br>Update</th>
                                                    <th>Followup <br>Since</th>
                                                    <th>Assigned by</th>
                                                    <th>Assigned to</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($achieved->count() > 0)
                                                    @foreach ($achieved as $followup)
                                                        @if (
                                                            $followup->created_by == Auth::user()->id ||
                                                                $followup->assigned_to == Auth::user()->id ||
                                                                in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $followup->area }}</td>
                                                                <td>{{ $followup->party_name }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-info"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#followPerson"
                                                                        data-id="{{ $followup->followPerson }}">
                                                                        See Person
                                                                    </button>
                                                                </td>

                                                                <td>{{ $followup->followup_for }}</td>
                                                                <td>{{ $followup->amount }}</td>
                                                                <td>
                                                                    {{ $followup->frequency ? $ferq[$followup->frequency] : '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->assign_initiate }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->latest_comment }}
                                                                    <br>
                                                                    <span class="text-muted text-success">
                                                                        on
                                                                        {{ date('d M y', strtotime($followup->updated_at)) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->format('d-m-Y') }}
                                                                    <br>
                                                                    {{ floor(\Carbon\Carbon::parse($followup->start_from)->diffInDays(now())) }}
                                                                    Days,
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->diffInHours(now()) % 24 }}
                                                                    Hrs
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->creator)->name ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->assignee)->name ?? '' }}
                                                                </td>
                                                                <td class="d-flex gap-2 flex-wrap">
                                                                    <a href="{{ route('followups.edit', $followup->id) }}"
                                                                        class="btn btn-xs btn-info">
                                                                        Initiate Auto Followup
                                                                    </a>
                                                                    <a type="button" class="btn btn-xs btn-primary"
                                                                        data-id="{{ $followup->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#updateFollowup">
                                                                        Update Status
                                                                    </a>
                                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                        <form
                                                                            action="{{ route('followups.destroy', $followup->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-xs btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this followup?')">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-angry" role="tabpanel"
                                    aria-labelledby="nav-angry-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="fdr">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Area</th>
                                                    <th>Organisation <br>Name</th>
                                                    <th>Concerned <br>Person</th>
                                                    <th>FollowUp <br>For</th>
                                                    <th>Amount</th>
                                                    <th>Frequency</th>
                                                    <th>Status</th>
                                                    <th>Last Status<br>Update</th>
                                                    <th>Followup <br>Since</th>
                                                    <th>Assigned by</th>
                                                    <th>Assigned to</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($angry->count() > 0)
                                                    @foreach ($angry as $followup)
                                                        @if (
                                                            $followup->created_by == Auth::user()->id ||
                                                                $followup->assigned_to == Auth::user()->id ||
                                                                in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $followup->area }}</td>
                                                                <td>{{ $followup->party_name }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-info"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#followPerson"
                                                                        data-id="{{ $followup->followPerson }}">
                                                                        See Person
                                                                    </button>
                                                                </td>

                                                                <td>{{ $followup->followup_for }}</td>
                                                                <td>{{ $followup->amount }}</td>
                                                                <td>
                                                                    {{ $followup->frequency ? $ferq[$followup->frequency] : '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->assign_initiate }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->latest_comment }}
                                                                    <br>
                                                                    <span class="text-muted text-success">
                                                                        on
                                                                        {{ date('d M y', strtotime($followup->updated_at)) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->format('d-m-Y') }}
                                                                    <br>
                                                                    {{ floor(\Carbon\Carbon::parse($followup->start_from)->diffInDays(now())) }}
                                                                    Days,
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->diffInHours(now()) % 24 }}
                                                                    Hrs
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->creator)->name ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->assignee)->name ?? '' }}
                                                                </td>
                                                                <td class="d-flex gap-2 flex-wrap">
                                                                    <a href="{{ route('followups.edit', $followup->id) }}"
                                                                        class="btn btn-xs btn-info">
                                                                        Initiate Auto Followup
                                                                    </a>
                                                                    <a type="button" class="btn btn-xs btn-primary"
                                                                        data-id="{{ $followup->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#updateFollowup">
                                                                        Update Status
                                                                    </a>
                                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                        <form
                                                                            action="{{ route('followups.destroy', $followup->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-xs btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this followup?')">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-future" role="tabpanel"
                                    aria-labelledby="nav-future-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="fdr">
                                            <thead>
                                                <tr>
                                                    <th>SN</th>
                                                    <th>Area</th>
                                                    <th>Organisation <br>Name</th>
                                                    <th>Concerned <br>Person</th>
                                                    <th>FollowUp <br>For</th>
                                                    <th>Amount</th>
                                                    <th>Frequency</th>
                                                    <th>Status</th>
                                                    <th>Last Status<br>Update</th>
                                                    <th>Followup <br>Since</th>
                                                    <th>Assigned by</th>
                                                    <th>Assigned to</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($future->count() > 0)
                                                    @foreach ($future as $followup)
                                                        @if (
                                                            $followup->created_by == Auth::user()->id ||
                                                                $followup->assigned_to == Auth::user()->id ||
                                                                in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $followup->area }}</td>
                                                                <td>{{ $followup->party_name }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-info"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#followPerson"
                                                                        data-id="{{ $followup->followPerson }}">
                                                                        See Person
                                                                    </button>
                                                                </td>

                                                                <td>{{ $followup->followup_for }}</td>
                                                                <td>{{ $followup->amount }}</td>
                                                                <td>
                                                                    {{ $followup->frequency ? $ferq[$followup->frequency] : '' }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->assign_initiate }}
                                                                </td>
                                                                <td>
                                                                    {{ $followup->latest_comment }}
                                                                    <br>
                                                                    <span class="text-muted text-success">
                                                                        on
                                                                        {{ date('d M y', strtotime($followup->updated_at)) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->format('d-m-Y') }}
                                                                    <br>
                                                                    {{ floor(\Carbon\Carbon::parse($followup->start_from)->diffInDays(now())) }}
                                                                    Days,
                                                                    {{ \Carbon\Carbon::parse($followup->start_from)->diffInHours(now()) % 24 }}
                                                                    Hrs
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->creator)->name ?? '' }}
                                                                </td>
                                                                <td>
                                                                    {{ optional($followup->assignee)->name ?? '' }}
                                                                </td>
                                                                <td class="d-flex gap-2 flex-wrap">
                                                                    <a href="{{ route('followups.edit', $followup->id) }}"
                                                                        class="btn btn-xs btn-info">
                                                                        Initiate Auto Followup
                                                                    </a>
                                                                    <a type="button" class="btn btn-xs btn-primary"
                                                                        data-id="{{ $followup->id }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#updateFollowup">
                                                                        Update Status
                                                                    </a>
                                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                        <form
                                                                            action="{{ route('followups.destroy', $followup->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-xs btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this followup?')">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
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

    <div class="modal fade" id="updateFollowup" tabindex="-1" aria-labelledby="updateFollowupLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFollowupLabel">Update Status</h5>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12 pt-1">
                            <div class="form-group">
                                <label class="form-label" for="latest_comment">Comment</label>
                                <textarea name="latest_comment" class="form-control" id="latest_comment" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 pt-3">
                            <div class="form-group">
                                <label class="form-label" for="frequency">Followup Frequency:</label>
                                <select name="frequency" id="frequency" class="form-control">
                                    <option value="">choose</option>
                                    @foreach ($ferq as $fr => $frq)
                                        <option value="{{ $fr }}">{{ $frq }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 pt-3 stop" style="display: none">
                            <div class="form-group">
                                <label class="form-label" for="stop_reason">Why Stop:</label>
                                <select name="stop_reason" class="form-control" id="stop_reason">
                                    <option value="">choose</option>
                                    <option value="1">The person is getting angry/or has requested to stop</option>
                                    <option value="2">Followup Objective achieved</option>
                                    <option value="3">External Followup Initiated</option>
                                    <option value="4">Remarks</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 pt-3 stop_proof" style="display: none">
                            <div class="form-group">
                                <label class="form-label">Please give proof:</label>
                                <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                <input type="file" name="proof_img" class="form-control mt-2" id="proof_img">
                            </div>
                        </div>
                        <div class="col-md-12 pt-3 stop_rem" style="display: none">
                            <div class="form-group">
                                <label class="form-label">Write Remarks:</label>
                                <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                            </div>
                        </div>
                        <div class="form-group col-md-6 pt-3">
                            <input type="submit" class="btn btn-primary btn-sm" value="Update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="followPerson" tabindex="-1" aria-labelledby="followPersonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followPersonLabel">Followup Person</h5>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#updateFollowup').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);
                modal.find('form').attr('action', `{{ route('updateFollowup', ':id') }}`.replace(':id',
                    id));
            });

            $("select[name='frequency']").on('change', function() {
                if ($(this).val() == '6') {
                    $('.stop').show();
                } else {
                    $('.stop').hide();
                }
            });

            $("select[name='stop_reason']").on('change', function() {
                if ($(this).val() == '2') {
                    $('.stop_proof').show();
                } else {
                    $('.stop_proof').hide();
                }
                if ($(this).val() == '4') {
                    $('.stop_rem').show();
                } else {
                    $('.stop_rem').hide();
                }
            });

            $('#followPerson').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var rawData = button.data('id');
                var parsedData = rawData;
                var modal = $(this);

                var htmlContent = '<ul>';
                parsedData.forEach(function(person) {
                    htmlContent += '<li><strong>Name:</strong> ' + person.name + '<br>' +
                        '<strong>Email:</strong> ' + person.email + '<br>' +
                        '<strong>Phone:</strong> ' + person.phone + '</li><hr>';
                });
                htmlContent += '</ul>';

                // Update the modal content
                modal.find('.modal-body').html(htmlContent);
            });

        });
    </script>
@endpush

@push('styles')
    <style>
        tr,
        th,
        td {
            padding: 8px;
        }
    </style>
@endpush
