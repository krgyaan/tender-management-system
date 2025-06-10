@extends('layouts.app')
@section('page-title', 'Bank Guarantee Dashboard')
@section('content')
    @php
        use Carbon\Carbon;
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
        $banks = [
            'SBI' => 'State Bank of India',
            'HDFC_0026' => 'HDFC Bank',
            'ICICI' => 'ICICI Bank',
            'YESBANK_2011' => 'Yes Bank 2011',
            'YESBANK_0771' => 'Yes Bank 0771',
            'PNB_6011' => 'Punjab National Bank',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                @if ($groupedBg)
                    <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center mb-3">
                        @foreach ($bankStats as $bankName => $stats)
                            <div class="p-3 rounded shadow border position-relative">
                                <h5 class="">
                                    {{ $banks[$bankName] }}
                                </h5>
                                <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-info">
                                    BG Created: {{ $stats['count'] }}
                                </span>
                                <p class="my-0 text-success">BG: ₹ {{ format_inr($stats['amount']) }}</p>
                                <p class="my-0 text-success">FDR (10%): ₹ {{ format_inr($stats['fdrAmount10']) }}</p>
                                <p class="my-0 text-success">FDR (15%): ₹ {{ format_inr($stats['fdrAmount15']) }}</p>
                                <p class="my-0 text-success">FDR (100%): ₹ {{ format_inr($stats['fdrAmount100']) }}</p>
                                <span
                                    class="position-absolute top-100 start-50 translate-middle badge rounded-pill bg-dark border border-light">
                                    {{ number_format($stats['percentage'], 2) }}% of BG
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
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
                                        <th>FDR Value</th>
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
                                                    <td>
                                                        <span class="d-none">{{ $bg->bg_expiry }}</span>
                                                        {{ date('d-m-Y', strtotime($bg->bg_expiry)) }}
                                                    </td>
                                                    <td>
                                                        <span class="d-none">{{ $bg->bg_claim }}</span>
                                                        {{ date('d-m-Y', strtotime($bg->bg_claim)) }}
                                                    </td>
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
                                                    <td>
                                                        @php
                                                            $bgValue = $bg->bg_amt ?? 0;
                                                            $stampPaper = $bg->stamp_charge_deducted ?? 0;
                                                            $bgStampPaperValue = 300;
                                                            $sfmsCharges = $bg->sfms_charge_deducted ?? 0;
                                                            $bgCreationDate = Carbon::parse($bg->created_at);
                                                            $bgClaimDate = Carbon::parse($bg->bg_claim);

                                                            $dailyInterestRate = 0.01 / 365;
                                                            $daysDifference = $bgClaimDate->diffInDays($bgCreationDate);
                                                            $interestComponent =
                                                                $bgValue * $dailyInterestRate * $daysDifference;
                                                            $interestWithGST = $interestComponent * 1.18;
                                                            $totalValue =
                                                                $interestWithGST +
                                                                $stampPaper +
                                                                $bgStampPaperValue +
                                                                $sfmsCharges;

                                                            echo format_inr($totalValue);
                                                        @endphp
                                                    </td>
                                                    <td>{{ $bg->fdr_no ?? '' }}</td>
                                                    <td>{{ format_inr($bg->fdr_amt) ?? 0 }}</td>
                                                    <td>
                                                        {{ $bg->emds->tender_id ? $bg->emds->tender->statuses->name ?? '' : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($bg->bg_expiry && $bg->bg_claim)
                                                            @if (now()->lte($bg->bg_expiry))
                                                                Valid
                                                            @elseif (now()->lte($bg->bg_claim))
                                                                Claim Period
                                                            @else
                                                                Expired
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
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
                                                        @else
                                                            {{ $bg->emds->type }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $tender = $bg->emds->tender;
                                                            $timer = $tender ? $tender->getTimer('bg_acc_form') : '';
                                                            if ($timer) {
                                                                $start = $timer->start_time;
                                                                $hrs = $timer->duration_hours;
                                                                $end = strtotime($start) + $hrs * 60 * 60;
                                                                $remaining = $end - time();
                                                            } else {
                                                                $remained = $tender
                                                                    ? $tender->remainedTime('bg_acc_form')
                                                                    : '';
                                                            }
                                                        @endphp
                                                        @if ($timer)
                                                            <span class="timer" id="timer-{{ $tender->id }}"
                                                                data-remaining="{{ $remaining }}"></span>
                                                        @else
                                                            {!! $remained !!}
                                                        @endif
                                                    </td>
                                                    <td class="d-flex flex-wrap gap-2">
                                                        @if ($bg->emds->type != 'Old Entries')
                                                            <a href="{{ route('bg-action', $bg->id) }}"
                                                                class="btn btn-xs btn-primary">
                                                                Status
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('emds-dashboard.show', $bg->emds->id) }}"
                                                            class="btn btn-xs btn-info">
                                                            View
                                                        </a>
                                                        <a href="{{ route('emds-dashboard.edit', $bg->emds->id) }}"
                                                            class="btn btn-xs btn-warning">
                                                            Edit
                                                        </a>
                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                            <form
                                                                action="{{ route('emds-dashboard.destroy', $bg->emds->id) }}"
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
                        <div class="pt-3">
                            <a href="{{ route('download-bgs') }}" class="btn btn-primary btn-sm">
                                Download All Bgs
                            </a>
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
