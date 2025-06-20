@extends('layouts.app')
@section('page-title', 'Demand Draft Dashboard')
@section('content')
    @php
        $ferq = [
            '1' => 'Daily',
            '2' => 'Alternate Days',
            '3' => '2 times a day',
            '4' => 'Weekly (every Mon)',
            '5' => 'Twice a Week (every Mon & Thu)',
            '6' => 'Stop',
        ];
        $instrumentType = [
            '0' => 'NA',
            '1' => 'Demand Draft',
            '2' => 'FDR',
            '3' => 'Cheque',
            '4' => 'BG',
            '5' => 'Bank Transfer',
            '6' => 'Pay on Portal',
        ];
        $ddStatus = [
            1 => 'Accounts Form (DD)',
            2 => 'Initiate Followup',
            3 => 'Returned via courier',
            4 => 'Returned via Bank Transfer',
            5 => 'Settled with Project Account',
            6 => 'Send DD Cancellation Request',
            7 => 'DD cancelled at Branch',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="text-center">
                            <a href="{{ route('dd-old-entry') }}" class="btn btn-info btn-sm">
                                Update Old Entries
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="dd">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap; max-width: 150px;">DD Date</th>
                                        <th>DD No</th>
                                        <th>Beneficiary name</th>
                                        <th>Tender Name</th>
                                        <th>Amount</th>
                                        <th>Tender Status</th>
                                        <th>Expiry</th>
                                        <th>DD Status</th>
                                        <th>Timer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($emdDd) > 0)
                                        @foreach ($emdDd as $dd)
                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator','account-executive','accountant','account-leader']) || Auth::user()->name == $dd->emd->requested_by)
                                                <tr>
                                                    <td style="white-space: nowrap; max-width: 150px;">{{ date('d-m-Y', strtotime($dd->created_at)) }}</td>
                                                    <td>{{ $dd->dd_no ?? '' }}</td>
                                                    <td>{{ $dd->dd_favour ?? '' }}</td>
                                                    <td>
                                                        {{ optional($dd->emd->tender)->tender_name ?? $dd->emd->project_name }}
                                                    </td>
                                                    <td>
                                                        {{ format_inr($dd->dd_amt) }}
                                                    </td>
                                                    <td class="text-capitalize">
                                                        {{ $dd->emd->tender->statuses->name ?? '' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $duedate = $dd->duedate ?? '';
                                                            if ($duedate) {
                                                                $currentDate = now();
                                                                $duedate = \Carbon\Carbon::parse($duedate);
                                                                $threeMonthsLater = $duedate->copy()->addMonths(3);

                                                                if ($currentDate->lte($threeMonthsLater)) {
                                                                    echo 'Valid';
                                                                } else {
                                                                    echo 'Expired';
                                                                }
                                                            } else {
                                                                echo 'No date';
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @if ($dd->action != null)
                                                            @switch($dd->action)
                                                                @case(1)
                                                                    @if ($dd->status == 'Accepted')
                                                                        {{ 'DD Created' }}
                                                                    @else
                                                                        {{ 'DD Rejected' }}
                                                                    @endif
                                                                @break

                                                                @case(2)
                                                                    {{ 'Followup Initiated' }}
                                                                @break

                                                                @case(3)
                                                                    {{ 'Returned via courier' }}
                                                                @break

                                                                @case(4)
                                                                    {{ 'Returned via Bank Transfer' }}
                                                                @break

                                                                @case(5)
                                                                    {{ 'Settled with Project Account' }}
                                                                @break

                                                                @case(6)
                                                                    {{ 'DD Cancellation request sent to branch' }}
                                                                @break

                                                                @case(7)
                                                                    {{ 'DD Cancelled at Branch' }}
                                                                @break

                                                                @default
                                                                    {{ 'NA' }}
                                                            @endswitch
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            if ($dd) {
                                                                $timer = $dd->getTimer('dd_ac_form');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $dd->remainedTime('dd_ac_form');
                                                                }
                                                            }
                                                        @endphp
                                                        @if (isset($dd) && $dd && isset($timer) && $timer)
                                                            <span class="timer" id="timer-{{ $dd->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @elseif (isset($dd) && $dd && isset($remained))
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td class="d-flex flex-wrap gap-2">
                                                        <a href="{{ route('dd-action', $dd->id) }}"
                                                            class="btn btn-xs btn-primary {{ !optional($dd->ddChq)->action ? 'disabled' : '' }}">
                                                            Status
                                                        </a>
                                                        <a href="{{ route('emds-dashboard.show', $dd->emd->id) }}"
                                                            class="btn btn-xs btn-info">
                                                            View
                                                        </a>
                                                        <a href="{{ route('emds-dashboard.edit', $dd->emd->id) }}" class="btn btn-xs btn-warning">
                                                            Edit
                                                        </a>
                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                            <form action="{{ route('emds-dashboard.destroy', $dd->emd->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this emd?');">
                                                                    Delete
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