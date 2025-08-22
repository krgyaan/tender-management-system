@extends('layouts.app')
@section('page-title', 'All EMD Dashboard')
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
        $popStatus = [
            1 => 'Accounts Form',
            2 => 'Initiate Followup',
            3 => 'Returned via Bank Transfer',
            4 => 'Settled with Project Account',
        ];
        $chqStatus = [
            1 => 'Accounts Form',
            2 => 'Initiate Followup',
            3 => 'Stop the cheque from the bank',
            4 => 'Paid via Bank Transfer',
            5 => 'Deposited in Bank',
            6 => 'Cancelled/Torn',
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

        $bgStatus = [
            1 => 'Accounts Form 1 - Request to Bank',
            2 => 'Accounts Form 2 - After BG Creation',
            3 => 'Accounts Form 3 - Capture FDR Details',
            4 => 'Initiate Followup',
            5 => 'Request Extension',
            6 => 'Returned via courier',
            7 => 'Request Cancellation',
            8 => 'BG Cancellation Confirmation',
            9 => 'FDR Cancellation Confirmation',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav class="navbar navbar-expand-lg">
                                <div class="nav nav-tabs mb-3 justify-content-between" id="nav-tab" role="tablist">
                                    <button class="nav-link btn btn-close-white active" id="dd-tab" data-bs-toggle="tab"
                                        data-bs-target="#dd" type="button" role="tab" aria-controls="dd"
                                        aria-selected="true">
                                        Demand Draft
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="fdr-tab" data-bs-toggle="tab"
                                        data-bs-target="#fdr" type="button" role="tab" aria-controls="fdr"
                                        aria-selected="false">
                                        Fixed Deposit Receipt (FDR)
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="cheque-tab" data-bs-toggle="tab"
                                        data-bs-target="#cheque" type="button" role="tab" aria-controls="cheque"
                                        aria-selected="false">
                                        Cheque
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="bg-tab" data-bs-toggle="tab"
                                        data-bs-target="#bg" type="button" role="tab" aria-controls="bg"
                                        aria-selected="false">
                                        Bank Guarantee (BG)
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="bt-tab" data-bs-toggle="tab"
                                        data-bs-target="#bt" type="button" role="tab" aria-controls="bt"
                                        aria-selected="false">
                                        Bank Transfer
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="pop-tab" data-bs-toggle="tab"
                                        data-bs-target="#pop" type="button" role="tab" aria-controls="pop"
                                        aria-selected="false">
                                        Pay on Portal
                                    </button>
                                </div>
                            </nav>
                            <div class="tab-content pt-3" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="dd-tab-content" role="tabpanel"
                                    aria-labelledby="dd-tab">
                                    <div class="text-center">
                                        <a href="{{ route('dd-old-entry') }}" class="btn btn-info btn-sm">
                                            Update Old Entries
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table" id="dd">
                                            <thead>
                                                <tr>
                                                    <th>DD Date</th>
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
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                                Str::startsWith('account', Auth::user()->role) ||
                                                                Auth::user()->name == $dd->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($dd->created_at)) }}</td>
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
                                                                            $threeMonthsLater = $duedate
                                                                                ->copy()
                                                                                ->addMonths(3);

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
                                                                        $tender = $dd->emd->tender;
                                                                        if ($tender) {
                                                                            $timer = $tender->getTimer('dd_ac_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $tender->remainedTime(
                                                                                    'dd_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($tender) && $tender && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $tender->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($tender) && $tender && isset($remained))
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
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="cheque-tab-content" role="tabpanel"
                                    aria-labelledby="cheque-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="cheque">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Payee name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Expiry</th>
                                                    <th>Cheque Status </th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdCheque) > 0)
                                                    @foreach ($emdCheque as $chq)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                                Str::startsWith('account', Auth::user()->role) ||
                                                                Auth::user()->name == $chq->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($chq->created_at)) }}</td>
                                                                <td>{{ $chq->cheq_no ?? '' }}</td>
                                                                <td>{{ $chq->cheque_favour ?? '' }}</td>
                                                                <td>{{ format_inr($chq->cheque_amt ?? '') }}</td>
                                                                <td>{{ $chq->cheque_reason ?? '' }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $duedate = $chq->duedate ?? '';
                                                                        if ($duedate) {
                                                                            $currentDate = now();
                                                                            $duedate = \Carbon\Carbon::parse($duedate);
                                                                            $threeMonthsLater = $duedate
                                                                                ->copy()
                                                                                ->addMonths(3);

                                                                            if ($currentDate->lte($threeMonthsLater)) {
                                                                                echo 'Valid';
                                                                            } else {
                                                                                echo 'Expired';
                                                                            }
                                                                        } else {
                                                                            if ($chq->status == 'DD Created') {
                                                                                echo 'Valid';
                                                                            } else {
                                                                                echo 'No date';
                                                                            }
                                                                        }
                                                                    @endphp
                                                                </td>
                                                                <td>
                                                                    @if ($chq->action != null)
                                                                        @switch($chq->action)
                                                                            @case(1)
                                                                                @if ($chq->status == 'Accepted')
                                                                                    {{ 'Cheque Created' }}
                                                                                @elseif($chq->status == 'Rejected')
                                                                                    {{ 'Cheque Rejected' }}
                                                                                @endif
                                                                            @break

                                                                            @case(2)
                                                                                {{ 'Followup Initiated' }}
                                                                            @break

                                                                            @case(3)
                                                                                {{ 'Cheque Stop Requested' }}
                                                                            @break

                                                                            @case(4)
                                                                                {{ 'Paid via Bank Transfer' }}
                                                                            @break

                                                                            @case(5)
                                                                                {{ 'Deposited in Bank' }}
                                                                            @break

                                                                            @case(6)
                                                                                {{ 'Cancelled/Torn' }}
                                                                            @break

                                                                            @default
                                                                                {{ 'NA' }}
                                                                        @endswitch
                                                                    @else
                                                                        {{ $chq->status }}
                                                                    @endIf
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $tender = $chq->emds->tender;
                                                                        if ($tender) {
                                                                            $timer = $tender->getTimer('cheque_ac_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $tender->remainedTime(
                                                                                    'cheque_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($tender) && $tender && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $tender->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($tender) && $tender && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td class="d-flex flex-wrap gap-2">
                                                                    <a href="{{ route('cheque-action', $chq->id) }}"
                                                                        class="btn btn-xs btn-primary">
                                                                        Status
                                                                    </a>
                                                                    <a href="{{ route('emds-dashboard.show', $chq->emds->id) }}"
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
                                <div class="tab-pane fade" id="bt-tab-content" role="tabpanel" aria-labelledby="bt-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="bt">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>UTR No</th>
                                                    <th>Account Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender Status</th>
                                                    <th>Bank Transfer Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdBt) > 0)
                                                    @foreach ($emdBt as $bt)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                                Str::startsWith('account', Auth::user()->role) ||
                                                                Auth::user()->name == $bt->emd->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($bt->created_at)) }}</td>
                                                                <td>{{ $bt->utr ?? '' }}</td>
                                                                <td>{{ $bt->bt_acc_name ?? '' }}</td>
                                                                <td>{{ optional($bt->emd->tender)->tender_name ?? $bt->emd->project_name }}
                                                                </td>
                                                                <td>{{ $bt->emd->tender->statuses->name ?? '' }}</td>
                                                                <td>{{ $bt->status ?? '' }}</td>
                                                                <td>
                                                                    @php
                                                                        $tender = $bt->emd->tender;
                                                                        if ($tender) {
                                                                            $timer = $tender->getTimer('bt_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $tender->remainedTime(
                                                                                    'bt_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($tender) && $timer)
                                                                        <span class="timer"
                                                                            id="timer-{{ $tender->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($tender) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td class="d-flex flex-wrap gap-2">
                                                                    <a class="btn btn-xs btn-primary"
                                                                        href="{{ route('bt-action', $bt->id) }}">
                                                                        Status
                                                                    </a>
                                                                    <a href="{{ route('emds-dashboard.show', $bt->emd->id) }}"
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
                                <div class="tab-pane fade" id="pop-tab-content" role="tabpanel"
                                    aria-labelledby="pop-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="pop">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>UTR No</th>
                                                    <th>Portal Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>Tender Status</th>
                                                    <th>POP Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdPop) > 0)
                                                    @foreach ($emdPop as $pop)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                                Str::startsWith('account', Auth::user()->role) ||
                                                                Auth::user()->name == $pop->emd->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($pop->created_at)) }}</td>
                                                                <td>{{ $pop->utr ?? '' }}
                                                                </td>
                                                                <td>{{ $pop->portal ?? '' }}
                                                                </td>
                                                                <td>{{ $pop->emd->project_name }}</td>
                                                                <td>{{ format_inr($pop->amount) }}
                                                                </td>
                                                                <td>{{ $pop->emd->tender->statuses->name ?? '' }}</td>
                                                                <td>{{ $pop->status ?? '' }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $tender = $pop->emd->tender;
                                                                        if ($tender) {
                                                                            $timer = $tender->getTimer('pop_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $tender->remainedTime(
                                                                                    'pop_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($tender) && $timer)
                                                                        <span class="timer"
                                                                            id="timer-{{ $tender->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($tender) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td class="d-flex flex-wrap gap-2">
                                                                    <a class="btn btn-xs btn-primary"
                                                                        href="{{ route('pop-action', $pop->id) }}">
                                                                        Status
                                                                    </a>
                                                                    <a href="{{ route('emds-dashboard.show', $pop->emd->id) }}"
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
                                <div class="tab-pane fade" id="fdr-tab-content" role="tabpanel"
                                    aria-labelledby="fdr-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="fdr">
                                            <thead>
                                                <tr>
                                                    <th>DD Date</th>
                                                    <th>DD No.</th>
                                                    <th>Beneficiary name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>Tender Status</th>
                                                    <th>Expiry</th>
                                                    <th>DD Status</th>
                                                    <th>Timer</th>
                                                    <th>Requested By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bg-tab-content" role="tabpanel" aria-labelledby="bg-tab">
                                    <div class="text-center">
                                        <a href="{{ route('bg-old-entry') }}" class="btn btn-info btn-sm">
                                            Update Old Entries
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table" id="bg">
                                            <thead>
                                                <tr>
                                                    <th>BG Date</th>
                                                    <th>BG No.</th>
                                                    <th>Beneficiary name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>BG Expiry Date</th>
                                                    <th>BG Claim Period<br> Expiry Date</th>
                                                    <th>BG Charges paid</th>
                                                    <th>BG Charges <br>Calculated</th>
                                                    <th>FDR No</th>
                                                    <th>Tender Status</th>
                                                    <th>Expiry</th>
                                                    <th>BG Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($emdBg && count($emdBg) > 0)
                                                    @foreach ($emdBg as $bg)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator']) ||
                                                                Str::startsWith('account', Auth::user()->role) ||
                                                                Auth::user()->name == $bg->emds->requested_by)
                                                            <tr>
                                                                <td>{{ $bg->created_at->format('d-m-Y') }}</td>
                                                                <td>{{ $bg->bg_no ?? '' }}</td>
                                                                <td>{{ $bg->bg_favour ?? '' }}</td>
                                                                <td>{{ $bg->emds->project_name }}</td>
                                                                <td>{{ format_inr($bg->bg_amt) ?? 0 }}</td>
                                                                <td>{{ date('d-m-Y', strtotime($bg->bg_expiry)) }}</td>
                                                                <td>{{ date('d-m-Y', strtotime($bg->bg_claim)) }}</td>
                                                                <td>
                                                                    @php
                                                                        $bgc = $bg->bg_charge_deducted ?? 0;
                                                                        $sfms = $bg->sfms_charge_deducted ?? 0;
                                                                        $stamp = $bg->stamp_charge_deducted ?? 0;
                                                                        $other = $bg->other_charge_deducted ?? 0;
                                                                        $total = $bgc + $sfms + $stamp + $other;
                                                                        echo format_inr($total);
                                                                    @endphp
                                                                </td>
                                                                <td>{{ format_inr($bg->bg_calculated ?? 0) }}</td>
                                                                <td>{{ $bg->dd_no ?? '' }}</td>
                                                                <td>{{ $bg->emds->tender_id != '00' ? '' : $bg->emds->tender->statuses->name ?? '' }}</td>
                                                                <td>{{ $bg->tender_expiry }}</td>
                                                                <td>
                                                                    @if ($bg->action)
                                                                        @switch($bg->action)
                                                                            @case(1)
                                                                                <span
                                                                                    class="{{ $bg->bg_req == 'Accepted' ? 'text-success' : 'text-danger' }}">
                                                                                    {{ $bg->bg_req == 'Accepted' ? 'Format Accepted' : 'Rejected' }}
                                                                                </span>
                                                                            @break

                                                                            @case(2)
                                                                                <span class="text-info">Created</span>
                                                                            @break

                                                                            @case(3)
                                                                                <span class="text-info">SFMS Submitted</span>
                                                                            @break

                                                                            @case(4)
                                                                                <span class="text-info">Followup Initiated</span>
                                                                            @break

                                                                            @case(5)
                                                                                <span class="text-info">Extension Request</span>
                                                                            @break

                                                                            @case(6)
                                                                                <span class="text-info">Returned via courier</span>
                                                                            @break

                                                                            @case(7)
                                                                                <span class="text-info">Cancellation Request</span>
                                                                            @break

                                                                            @case(8)
                                                                                <span class="text-info">BG Cancelled</span>
                                                                            @break

                                                                            @case(9)
                                                                                <span class="text-info">FDR released</span>
                                                                            @break

                                                                            @default
                                                                                <span class="text-info"></span>
                                                                        @endswitch
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $tender = $bg->emds->tender;
                                                                        if ($tender) {
                                                                            $timer = $tender->getTimer('bg_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $tender->remainedTime(
                                                                                    'bg_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($tender) && $timer)
                                                                        <span class="timer"
                                                                            id="timer-{{ $tender->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($tender) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td class="d-flex flex-wrap gap-2">
                                                                    <a href="{{ route('bg-action', $bg->id) }}"
                                                                        class="btn btn-xs btn-primary">
                                                                        Status
                                                                    </a>
                                                                    <a href="{{ route('emds-dashboard.show', $bg->emds->id) }}"
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
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="tenderBtFeeModal" tabindex="-1" aria-labelledby="tenderBtFeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderBtFeeModalLabel">Bank Transfer Tender Fees</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tender-fees.bt.store') }}">
                        @csrf
                        <div class="row" id="bank_transfer">
                            <div class="col-md-12 form-group">
                                <input type="hidden" name="tender_id" value="">
                                <input type="hidden" name="emd_id" value="">
                                <label class="form-label" for="purpose">Purpose</label>
                                <input type="text" name="purpose" id="purpose" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="bt_acc_name">
                                    Account Name
                                </label>
                                <input type="text" name="account_name" id="account_name" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('account_name') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="account_number">Account Number</label>
                                <input type="text" name="account_number" id="account_number" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('account_number') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="ifsc">IFSC</label>
                                <input type="text" name="ifsc" id="ifsc" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('ifsc') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" step="any" name="amount" id="amount"
                                    class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tenderPopFeeModal" tabindex="-1" aria-labelledby="tenderPopFeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderPopFeeModalLabel">Pop Tender Fees</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tender-fees.pop.store') }}">
                        @csrf
                        <div class="row" id="bank_transfer">
                            <div class="col-md-12 form-group">
                                <input type="hidden" name="tender_id" value="">
                                <input type="hidden" name="emd_id" value="">
                                <label class="form-label" for="purpose">Purpose</label>
                                <input type="text" name="purpose" id="purpose" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="portal_name">Name of Portal</label>
                                <input type="text" name="portal_name" id="portal_name" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('portal_name') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="netbanking">Netbanking available</label>
                                <select name="netbanking" id="netbanking" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('netbanking') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="bank_debit_card">Yes Bank Debit card</label>
                                <select name="bank_debit_card" id="bank_debit_card" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('bank_debit_card') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" step="any" name="amount" id="amount"
                                    class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
            $('#tenderBtFeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender_id');
                var emdId = button.data('emd_id');
                $(this).find('input[name="tender_id"]').val(tenderId);
                $(this).find('input[name="emd_id"]').val(emdId);
            });

            $('#tenderPopFeeModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var tenderId = button.data('tender_id');
                var emdId = button.data('emd_id');
                $(this).find('input[name="tender_id"]').val(tenderId);
                $(this).find('input[name="emd_id"]').val(emdId);
            });

            let buttons = document.querySelectorAll('button.nav-link');
            let tabContents = document.querySelectorAll('.tab-pane');

            function activateTab(button) {
                buttons.forEach(function(btn) {
                    btn.classList.remove('active');
                });

                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                    content.classList.remove('show');
                });

                button.classList.add('active');
                let tabId = button.getAttribute('id');

                let activeContent = document.querySelector(`#${tabId}-content`);
                if (activeContent) {
                    activeContent.classList.add('active');
                    activeContent.classList.add('show');
                }

                localStorage.setItem('activeTab', tabId);
            }

            buttons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    activateTab(button);
                });
            });

            window.addEventListener('load', function() {
                let activeTabId = localStorage.getItem('activeTab');
                if (activeTabId) {
                    let activeButton = document.getElementById(activeTabId);
                    if (activeButton) {
                        activateTab(activeButton);
                    }
                } else {
                    let firstButton = buttons[0];
                    activateTab(firstButton);
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
