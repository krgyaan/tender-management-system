@extends('layouts.app')
@section('page-title', 'Cheque Dashboard')
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
        $chqStatus = [
            1 => 'Accounts Form',
            2 => 'Initiate Followup',
            3 => 'Stop the cheque from the bank',
            4 => 'Paid via Bank Transfer',
            5 => 'Deposited in Bank',
            6 => 'Cancelled/Torn',
        ];
    @endphp
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
                                        aria-selected="true">Cheque Pending</button>
                                        
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Cheque Payable</button>
                                        
                                    <button class="nav-link" id="nav-paid-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-paid" type="button" role="tab"
                                        aria-controls="nav-paid" aria-selected="false">Cheque Paid/stop</button>
                                        
                                    <button class="nav-link" id="nav-fdrdd-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-fdrdd" type="button" role="tab"
                                        aria-controls="nav-fdrdd" aria-selected="false">Cheque for Security/DD/FDR</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="cheque">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Payee name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Cheque<br>Due Date</th>
                                                    <th>Expiry</th>
                                                    <th>Cheque Status </th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdChequePending) > 0)
                                                    @foreach ($emdChequePending as $chq)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $chq->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($chq->created_at)) }}</td>
                                                                <td>{{ $chq->cheq_no ?? '' }}</td>
                                                                <td>{{ $chq->cheque_favour ?? '' }}</td>
                                                                <td>{{ format_inr($chq->cheque_amt ?? '') }}</td>
                                                                <td>{{ $chq->cheque_reason ?? '' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($chq->cheque_date)->format('d-m-Y h:i A') }}
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
                                                                        if ($chq) {
                                                                            $timer = $chq->getTimer(
                                                                                'cheque_ac_form',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $chq->remainedTime(
                                                                                    'cheque_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($chq) && $chq && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $chq->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($chq) && $chq && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="{{ route('cheque-action', $chq->id) }}"
                                                                            class="btn btn-xs btn-primary">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $chq->emds->id) }}"
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
                                        <table class="table" id="cheque">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Payee name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Cheque<br>Due Date</th>
                                                    <th>Expiry</th>
                                                    <th>Cheque Status </th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdChequePayable) > 0)
                                                    @foreach ($emdChequePayable as $chq)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $chq->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($chq->created_at)) }}</td>
                                                                <td>{{ $chq->cheq_no ?? '' }}</td>
                                                                <td>{{ $chq->cheque_favour ?? '' }}</td>
                                                                <td>{{ format_inr($chq->cheque_amt ?? '') }}</td>
                                                                <td>{{ $chq->cheque_reason ?? '' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($chq->duedate)->format('d-m-Y h:i A') }}
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
                                                                        if ($chq) {
                                                                            $timer = $chq->getTimer(
                                                                                'cheque_ac_form',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $chq->remainedTime(
                                                                                    'cheque_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($chq) && $chq && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $chq->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($chq) && $chq && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="{{ route('cheque-action', $chq->id) }}"
                                                                            class="btn btn-xs btn-primary">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $chq->emds->id) }}"
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
                                <div class="tab-pane fade" id="nav-paid" role="tabpanel"
                                    aria-labelledby="nav-paid-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="cheque">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Payee name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Cheque<br>Due Date</th>
                                                    <th>Expiry</th>
                                                    <th>Cheque Status </th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdChequePaidStop) > 0)
                                                    @foreach ($emdChequePaidStop as $chq)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $chq->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($chq->created_at)) }}</td>
                                                                <td>{{ $chq->cheq_no ?? '' }}</td>
                                                                <td>{{ $chq->cheque_favour ?? '' }}</td>
                                                                <td>{{ format_inr($chq->cheque_amt ?? '') }}</td>
                                                                <td>{{ $chq->cheque_reason ?? '' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($chq->duedate)->format('d-m-Y h:i A') }}
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
                                                                        if ($chq) {
                                                                            $timer = $chq->getTimer(
                                                                                'cheque_ac_form',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $chq->remainedTime(
                                                                                    'cheque_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($chq) && $chq && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $chq->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($chq) && $chq && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="{{ route('cheque-action', $chq->id) }}"
                                                                            class="btn btn-xs btn-primary">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $chq->emds->id) }}"
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
                                <div class="tab-pane fade" id="nav-fdrdd" role="tabpanel"
                                    aria-labelledby="nav-fdrdd-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="cheque">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Payee name</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Cheque<br>Due Date</th>
                                                    <th>Expiry</th>
                                                    <th>Cheque Status </th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($emdChequeSecFDRDD) > 0)
                                                    @foreach ($emdChequeSecFDRDD as $chq)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $chq->emds->requested_by)
                                                            <tr>
                                                                <td>{{ date('d-m-Y', strtotime($chq->created_at)) }}</td>
                                                                <td>{{ $chq->cheq_no ?? '' }}</td>
                                                                <td>{{ $chq->cheque_favour ?? '' }}</td>
                                                                <td>{{ format_inr($chq->cheque_amt ?? '') }}</td>
                                                                <td>{{ $chq->cheque_reason ?? '' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($chq->duedate)->format('d-m-Y h:i A') }}
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
                                                                        if ($chq) {
                                                                            $timer = $chq->getTimer(
                                                                                'cheque_ac_form',
                                                                            );
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $chq->remainedTime(
                                                                                    'cheque_ac_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($chq) && $chq && isset($timer) && $timer)
                                                                        <span class="timer" id="timer-{{ $chq->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($chq) && $chq && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="{{ route('cheque-action', $chq->id) }}"
                                                                            class="btn btn-xs btn-primary">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $chq->emds->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $chq->emds->id) }}"
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