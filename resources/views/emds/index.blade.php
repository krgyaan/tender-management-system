@extends('layouts.app')
@section('page-title', 'All EMD Eligible Tenders')
@section('content')
    @php
        $user = Auth::user();
        $permissions = explode(',', $user->permissions);
        $hasAccess = in_array($user->role, ['admin', 'coordinator']) || Str::startsWith('account', $user->role);
        $hasEmdPermission = in_array('request-emd', $permissions);
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('emds.create') }}" class="btn btn-primary btn-sm">
                        Request EMD (other than tms)
                    </a>
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#old-entries-modal">
                        EMD Old Entries
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#without-tender-modal">
                        EMD Other Than Tender
                    </button>
                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#tender-fees-modal">
                        Tender Fees (other than TMS)
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">EMD Request Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">EMD Request Sent</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br>Values</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>Tender EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due date/time</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pendingTenders as $tender)
                                                    @if ($hasAccess || $tender->users->id == $user->id)
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $timer = $tender->getTimer('emd_request');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tender->remainedTime(
                                                                            'emd_request',
                                                                        );
                                                                    }
                                                                @endphp
                                                                @if ($timer)
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>
                                                            <td @class(['d-flex', 'flex-wrap', 'gap-2'])>
                                                                @if ($tender->emds->count() > 0)
                                                                    <a href="{{ route('emds.edit', $tender->id) }}"
                                                                        class="btn btn-primary btn-xs">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('emds.show', $tender->id) }}"
                                                                        class="btn btn-xs btn-primary">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('emds.create', base64_encode($tender->tender_no)) }}"
                                                                        class="btn btn-info btn-xs">
                                                                        Request EMD
                                                                    </a>
                                                                @endif
                                                                @if (
                                                                    $tender->gst_values > 0 &&
                                                                        ($tender->emds->count() > 0 ? in_array($tender->emds->first()->instrument_type, [1, 5, 6]) : false))
                                                                    <a class="btn btn-secondary btn-xs"
                                                                        href="{{ route('tender-fees.create-direct', $tender->id) }}">
                                                                        Tender Fees
                                                                    </a>
                                                                @endif
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
                                        <table class="table" id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br>Values</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>Tender EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due date/time</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sentTenders as $tender)
                                                    @if ($hasAccess || $tender->users->id == $user->id)
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $timer = $tender->getTimer('emd_request');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    } else {
                                                                        $remained = $tender->remainedTime(
                                                                            'emd_request',
                                                                        );
                                                                    }
                                                                @endphp
                                                                @if ($timer)
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>
                                                            <td @class(['d-flex', 'flex-wrap', 'gap-2'])>
                                                                @if ($tender->emds->count() > 0)
                                                                    <a href="{{ route('emds.edit', $tender->id) }}"
                                                                        class="btn btn-primary btn-xs">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('emds.show', $tender->id) }}"
                                                                        class="btn btn-xs btn-primary">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('emds.create', base64_encode($tender->tender_no)) }}"
                                                                        class="btn btn-info btn-xs">
                                                                        Request EMD
                                                                    </a>
                                                                @endif
                                                                @if (
                                                                    $tender->gst_values > 0 &&
                                                                        ($tender->emds->count() > 0 ? in_array($tender->emds->first()->instrument_type, [1, 5, 6]) : false))
                                                                    <a class="btn btn-secondary btn-xs"
                                                                        href="{{ route('tender-fees.create-direct', $tender->id) }}">
                                                                        Tender Fees
                                                                    </a>
                                                                @endif
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

        <div class="modal fade" id="old-entries-modal" tabindex="-1" aria-labelledby="old-entries-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="old-entries-modal-label">Create Old Entries</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select BI Type for Old Entries</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('dd-old-entry') }}" class="btn btn-sm btn-light">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('bg-old-entry') }}" class="btn btn-sm btn-light">
                                    Bank Guarantee
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="without-tender-modal" tabindex="-1" aria-labelledby="without-tender-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="without-tender-modal-label">Create EMD for other than Tender</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select BI Type for other than Tender</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('cheque-ott-entry') }}" class="btn btn-sm btn-light">
                                    Cheque
                                </a>
                                <a href="{{ route('dd-ott-entry') }}" class="btn btn-sm btn-light">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('bg-ott-entry') }}" class="btn btn-sm btn-light">
                                    Bank Guarantee
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="tender-fees-modal" tabindex="-1" aria-labelledby="tender-fees-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tender-fees-modal-label">Tender Fees</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 py-3">
                                <h4 class="text-center">Select Tender Fees Format</h4>
                            </div>
                            <div class="col-md-12 d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('tender-fees.dd.create') }}" class="btn btn-sm btn-info">
                                    Demand Draft (DD)
                                </a>
                                <a href="{{ route('tender-fees.bt.create') }}" class="btn btn-sm btn-info">
                                    Bank Transfer
                                </a>
                                <a href="{{ route('tender-fees.pop.create') }}" class="btn btn-sm btn-info">
                                    Pay on Portal
                                </a>
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
        });
        $(document).ready(function() {
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
