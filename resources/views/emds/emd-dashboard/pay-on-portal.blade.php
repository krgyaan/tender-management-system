@extends('layouts.app')
@section('page-title', 'Pay On Portal Dashboard')
@section('content')
    @php
        use Illuminate\Support\Str;
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
                                        aria-selected="true">POP Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">POP Done</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="pop">
                                            <thead>
                                                <tr>
                                                    <th style="white-space: nowrap; max-width: 150px;">Date</th>
                                                    <th>Requested By</th>
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
                                                @if (count($emdPopPending) > 0)
                                                    @foreach ($emdPopPending as $pop)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $pop->emd->requested_by)
                                                            <tr>
                                                                <td style="white-space: nowrap; max-width: 150px;">
                                                                    {{ date('d-m-Y', strtotime($pop->created_at)) }}
                                                                </td>
                                                                <td>{{ $pop->emd->requested_by ?? '' }}</td>
                                                                <td>{{ $pop->utr ?? '' }}</td>
                                                                <td>
                                                                    <a href="{{ Str::startsWith($pop->portal ?? '', ['http://', 'https://']) ? $pop->portal : 'https://' . $pop->portal }}"
                                                                        target="_blank">
                                                                        {{ $pop->portal ?? '' }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $pop->emd->project_name }}</td>
                                                                <td>{{ format_inr($pop->amount) }}</td>
                                                                <td>{{ $pop->emd->tender->statuses->name ?? $pop->emd->type }}
                                                                </td>
                                                                <td>{{ $pop->status ?? '' }}</td>
                                                                <td>
                                                                    @php
                                                                        if ($pop) {
                                                                            $timer = $pop->getTimer('pop_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $pop->remainedTime(
                                                                                    'pop_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($pop) && $timer)
                                                                        <span class="timer" id="timer-{{ $pop->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($pop) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a class="btn btn-xs btn-primary"
                                                                            href="{{ route('pop-action', $pop->id) }}">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $pop->emd->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $pop->emd->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $pop->emd->id) }}"
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
                                        <table class="table" id="pop">
                                            <thead>
                                                <tr>
                                                    <th style="white-space: nowrap; max-width: 150px;">Date</th>
                                                    <th>Requested By</th>
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
                                                @if (count($emdPopDone) > 0)
                                                    @foreach ($emdPopDone as $pop)
                                                        @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']) ||
                                                                Auth::user()->name == $pop->emd->requested_by)
                                                            <tr>
                                                                <td style="white-space: nowrap; max-width: 150px;">
                                                                    {{ date('d-m-Y', strtotime($pop->created_at)) }}
                                                                </td>
                                                                <td>{{ $pop->emd->requested_by ?? '' }}</td>
                                                                <td>{{ $pop->utr ?? '' }}</td>
                                                                <td>
                                                                    <a href="{{ Str::startsWith($pop->portal ?? '', ['http://', 'https://']) ? $pop->portal : 'https://' . $pop->portal }}"
                                                                        target="_blank">
                                                                        {{ $pop->portal ?? '' }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $pop->emd->project_name }}</td>
                                                                <td>{{ format_inr($pop->amount) }}</td>
                                                                <td>{{ $pop->emd->tender->statuses->name ?? $pop->emd->type }}
                                                                </td>
                                                                <td>{{ $pop->status ?? '' }}</td>
                                                                <td>
                                                                    @php
                                                                        if ($pop) {
                                                                            $timer = $pop->getTimer('pop_acc_form');
                                                                            if ($timer) {
                                                                                $start = $timer->start_time;
                                                                                $hrs = $timer->duration_hours;
                                                                                $end =
                                                                                    strtotime($start) + $hrs * 60 * 60;
                                                                                $remaining = $end - time();
                                                                            } else {
                                                                                $remained = $pop->remainedTime(
                                                                                    'pop_acc_form',
                                                                                );
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if (isset($pop) && $timer)
                                                                        <span class="timer" id="timer-{{ $pop->id }}"
                                                                            data-remaining="{{ $remaining }}"></span>
                                                                    @elseif (isset($pop) && isset($remained))
                                                                        {!! $remained !!}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a class="btn btn-xs btn-primary"
                                                                            href="{{ route('pop-action', $pop->id) }}">
                                                                            Status
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.show', $pop->emd->id) }}"
                                                                            class="btn btn-xs btn-info">
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('emds-dashboard.edit', $pop->emd->id) }}"
                                                                            class="btn btn-xs btn-warning">
                                                                            Edit
                                                                        </a>
                                                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                                                                            <form
                                                                                action="{{ route('emds-dashboard.destroy', $pop->emd->id) }}"
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
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
