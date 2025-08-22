@extends('layouts.app')
@section('page-title', 'FDR Dashboard')
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
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="table-responsive">
                            <table class="table" id="fdr">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap; max-width: 150px;">FDR Date</th>
                                        <th>FDR No.</th>
                                        <th>Beneficiary name</th>
                                        <th>Tender/WO Name</th>
                                        <th>Amount</th>
                                        <th>Tender Status</th>
                                        <th>Expiry</th>
                                        <th>FDR Status</th>
                                        <th>Timer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($emdFdr) > 0)
                                        @foreach ($emdFdr as $fdr)
                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator','account-executive','accountant','account-leader']) || Auth::user()->name == $fdr->emds->requested_by)
                                                <tr>
                                                    <td style="white-space: nowrap; max-width: 150px;">
                                                        {{ $fdr->fdr_date ? date('d-m-Y', strtotime($fdr->fdr_date)) : '' }}
                                                    </td>
                                                    <td>{{ $fdr->fdr_no ?? '' }}</td>
                                                    <td>{{ $fdr->fdr_favour ?? '' }}</td>
                                                    <td>
                                                        {{ $fdr->emds?->project_name }}
                                                    </td>
                                                    <td>
                                                        {{ format_inr($fdr->fdr_amt) }}
                                                    </td>
                                                    <td class="text-capitalize">
                                                        {{ $fdr->emds->tender->statuses->name ?? '' }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $duedate = $fdr->duedate ?? '';
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
                                                        @if ($fdr->action != null)
                                                            @switch($fdr->action)
                                                                @case(1)
                                                                    @if ($fdr->status == 'Accepted')
                                                                        {{ 'FDR Created' }}
                                                                    @else
                                                                        {{ 'FDR Rejected' }}
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
                                                                    {{ 'FDR Cancellation request sent to branch' }}
                                                                @break

                                                                @case(7)
                                                                    {{ 'FDR Cancelled at Branch' }}
                                                                @break

                                                                @default
                                                                    {{ 'NA' }}
                                                            @endswitch
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            if ($fdr) {
                                                                $timer = $fdr->getTimer('fdr_ac_form');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $fdr->remainedTime('fdr_ac_form');
                                                                }
                                                            }
                                                        @endphp
                                                        @if (isset($fdr) && $fdr && isset($timer) && $timer)
                                                            <span class="timer" id="timer-{{ $fdr->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @elseif (isset($fdr) && $fdr && isset($remained))
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td class="d-flex flex-wrap gap-2">
                                                        <a href="{{ route('dd-action', $fdr->id) }}"
                                                            class="btn btn-xs btn-primary {{ !optional($fdr->ddChq)->action ? 'disabled' : '' }}">
                                                            Status
                                                        </a>
                                                        <a href="{{ route('emds-dashboard.show', $fdr->emds ? $fdr->emds->id : $fdr->id) }}"
                                                            class="btn btn-xs btn-info">
                                                            View
                                                        </a>
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