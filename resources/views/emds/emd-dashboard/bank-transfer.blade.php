@extends('layouts.app')
@section('page-title', 'Bank Transfer Dashboard')
@section('content')
    @php
        $popStatus = [
            1 => 'Accounts Form',
            2 => 'Initiate Followup',
            3 => 'Returned via Bank Transfer',
            4 => 'Settled with Project Account',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between">
                    @if (Auth::user()->role == 'admin')
                        <a href="{{ route('emds.export.bt') }}" class="btn btn-outline-success btn-sm">Export</a>
                    @endif
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Bank Transfer Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Bank Transfer Done</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="bt">
                                            <thead>
                                                <tr>
                                                    <th style="white-space: nowrap; max-width: 150px;">Date</th>
                                                    <th>Team</th>
                                                    <th>Member</th>
                                                    <th>UTR No</th>
                                                    <th>Account Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender Status</th>
                                                    <th>Amount</th>
                                                    <th>BT Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdBtPending) > 0)
                                                    @foreach ($emdBtPending as $bt)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $bt->emd->requested_by)
                                                            @php
                                                                $team =
                                                                    \App\Models\User::where(
                                                                        'name',
                                                                        $bt->emd->requested_by,
                                                                    )->first()->team ?? '';
                                                            @endphp
                                                            <tr style="white-space: nowrap; max-width: 150px;">
                                                                <td style="min-width: 100px;">
                                                                    {{ date('d-m-Y', strtotime($bt->created_at)) }}</td>
                                                                <td>{{ $bt->emd->tender->team ?? $team }}
                                                                </td>
                                                                <td>{{ $bt->emd->requested_by ?? '' }}</td>
                                                                <td>{{ $bt->utr ?? '' }}</td>
                                                                <td>{{ $bt->bt_acc_name ?? '' }}</td>
                                                                <td>{{ optional($bt->emd->tender)->tender_name ?? $bt->emd->project_name }}
                                                                </td>
                                                                <td>{{ $bt->emd->tender->statuses->name ?? $bt->emd->type }}
                                                                </td>
                                                                <td>{{ format_inr($bt->bt_amount) ?? '' }}</td>
                                                                <td>{{ $bt->status ?? '' }}</td>
                                                                <td>
                                                                    @php
                                                                        if ($bt) {
                                                                            $timer = $bt->getTimer('bt_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $bt->remainedTime(
                                                                                    'bt_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($bt) && $timer)
                                                                        <span class="timer" id="timer-{{ $bt->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($bt) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a class="btn btn-xs btn-primary"
                                                                            href="{{ route('bt-action', $bt->id) }}">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $bt->emd->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $bt->emd->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $bt->emd->id) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="btn btn-xs btn-danger"
                                                                                    onclick="return confirm('Are you sure you want to delete this emd?');">
                                                                                    Delete
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="bt">
                                            <thead>
                                                <tr>
                                                    <th style="white-space: nowrap; max-width: 150px;">Date</th>
                                                    <th>Team</th>
                                                    <th>Member</th>
                                                    <th>UTR No</th>
                                                    <th>Account Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender Status</th>
                                                    <th>Amount</th>
                                                    <th>BT Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdBtDone) > 0)
                                                    @foreach ($emdBtDone as $bt)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $bt->emd->requested_by)
                                                            @php
                                                                $team =
                                                                    \App\Models\User::where(
                                                                        'name',
                                                                        $bt->emd->requested_by,
                                                                    )->first()->team ?? '';
                                                            @endphp
                                                            <tr style="white-space: nowrap; max-width: 150px;">
                                                                <td style="min-width: 100px;">
                                                                    {{ date('d-m-Y', strtotime($bt->created_at)) }}</td>
                                                                <td>{{ $bt->emd->tender->team ?? $team }}
                                                                </td>
                                                                <td>{{ $bt->emd->requested_by ?? '' }}</td>
                                                                <td>{{ $bt->utr ?? '' }}</td>
                                                                <td>{{ $bt->bt_acc_name ?? '' }}</td>
                                                                <td>{{ optional($bt->emd->tender)->tender_name ?? $bt->emd->project_name }}
                                                                </td>
                                                                <td>{{ $bt->emd->tender->statuses->name ?? $bt->emd->type }}
                                                                </td>
                                                                <td>{{ format_inr($bt->bt_amount) ?? '' }}</td>
                                                                <td>{{ $bt->status ?? '' }}</td>
                                                                <td>
                                                                    @php
                                                                        if ($bt) {
                                                                            $timer = $bt->getTimer('bt_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $bt->remainedTime(
                                                                                    'bt_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($bt) && $timer)
                                                                        <span class="timer" id="timer-{{ $bt->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($bt) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a class="btn btn-xs btn-primary"
                                                                            href="{{ route('bt-action', $bt->id) }}">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $bt->emd->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $bt->emd->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $bt->emd->id) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="btn btn-xs btn-danger"
                                                                                    onclick="return confirm('Are you sure you want to delete this emd?');">
                                                                                    Delete
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
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
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
