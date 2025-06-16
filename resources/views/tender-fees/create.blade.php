@extends('layouts.app')
@section('page-title', 'Tender Fee Create')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('emds.index') }}" class="btn btn-sm btn-outline-danger">Back</a>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">
                            Tender Fees via
                            @if ($instrumentType == '5')
                                Bank Transfer
                            @elseif($instrumentType == '1')
                                Demand Draft
                            @elseif($instrumentType == '6')
                                Pay on Portal
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST"
                            action="{{ $instrumentType == '5'
                                ? route('tender-fees.bt.store')
                                : ($instrumentType == '1'
                                    ? route('tender-fees.dd.store')
                                    : route('tender-fees.pop.store')) }}">
                            @csrf
                            <div class="row">
                                {{-- Common Fields --}}
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="tender_name">Tender Name</label>
                                    <input type="text" name="tender_name" id="tender_name" class="form-control"
                                        value="{{ $emd?->project_name ?? old('tender_name') }}"
                                        placeholder="Enter Tender Name" required>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                    </small>

                                    <input type="hidden" name="tender_id" value="{{ $emd?->tender_id ?? 0 }}">
                                    <input type="hidden" name="emd_id" value="{{ $emd?->id ?? 0 }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="due_date_time">Due date and time</label>
                                    <input type="datetime-local" name="due_date_time" id="due_date_time"
                                        class="form-control"
                                        value="{{ $emd?->due_date ? "{$emd?->tender->due_date}T{$emd?->tender->due_time}" : old('due_date_time') }}"
                                        placeholder="Select Due Date and Time" required>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('due_date_time') }}</span>
                                    </small>
                                </div>

                                {{-- Bank Transfer Fields --}}
                                @if ($instrumentType == '5')
                                    @php
                                        $bt = $emd?->emdBankTransfers->first();
                                    @endphp
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="purpose">Purpose</label>
                                        <input type="text" name="purpose" id="purpose" class="form-control"
                                            value="{{ $bt?->purpose ?? old('purpose') }}" placeholder="Enter Purpose"
                                            required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="account_name">Account Name</label>
                                        <input type="text" name="account_name" id="account_name" class="form-control"
                                            value="{{ $bt?->bt_acc_name ?? old('account_name') }}"
                                            placeholder="Enter Account Name" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('account_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="account_number">Account Number</label>
                                        <input type="text" name="account_number" id="account_number" class="form-control"
                                            value="{{ $bt?->bt_acc ?? old('account_number') }}"
                                            placeholder="Enter Account Number" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('account_number') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="ifsc">IFSC</label>
                                        <input type="text" name="ifsc" id="ifsc" class="form-control"
                                            value="{{ $bt?->bt_ifsc ?? old('ifsc') }}" placeholder="Enter IFSC" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('ifsc') }}</span>
                                        </small>
                                    </div>

                                    {{-- Demand Draft Fields --}}
                                @elseif($instrumentType == '1')
                                    @php
                                        $dd = $emd?->emdDemandDrafts->first();
                                    @endphp
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="dd_needed_in">DD deliver by</label>
                                        <select name="dd_needs" id="dd_needs" class="form-control" required>
                                            <option value="">-- Choose --</option>
                                            <option {{ $dd?->dd_needs == 'due' ? 'selected' : '' }} value="due">Tender
                                                Due Date</option>
                                            <option {{ $dd?->dd_needs == '24' ? 'selected' : '' }} value="24">24 Hours
                                            </option>
                                            <option {{ $dd?->dd_needs == '36' ? 'selected' : '' }} value="36">36 Hours
                                            </option>
                                            <option {{ $dd?->dd_needs == '48' ? 'selected' : '' }} value="48">48 Hours
                                            </option>
                                        </select>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('dd_needs') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="purpose_of_dd">Purpose of DD</label>
                                        <input type="text" name="purpose_of_dd" id="purpose_of_dd"
                                            class="form-control" value="{{ $dd?->dd_purpose ?? old('purpose_of_dd') }}"
                                            placeholder="Enter Purpose of DD" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('purpose_of_dd') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="in_favour_of">DD in favour of</label>
                                        <input type="text" name="in_favour_of" id="in_favour_of" class="form-control"
                                            value="{{ $dd?->dd_favour ?? old('in_favour_of') }}"
                                            placeholder="Enter DD in Favour of" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('in_favour_of') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="dd_payable_at">DD Payable at</label>
                                        <input type="text" name="dd_payable_at" id="dd_payable_at"
                                            class="form-control" value="{{ $dd?->dd_payable ?? old('dd_payable_at') }}"
                                            placeholder="Enter DD Payable at" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('dd_payable_at') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="courier_address">Courier Address</label>
                                        <input type="text" name="courier_address" id="courier_address"
                                            class="form-control"
                                            value="{{ $dd?->courier_add ?? old('courier_address') }}"
                                            placeholder="Enter Courier Address" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('courier_address') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="delivery_date_time">
                                            Time required for courier to reach destination:
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="courier_deadline" id="courier_deadline"
                                                class="form-control"
                                                value="{{ $dd?->courier_deadline ?? old('courier_deadline') }}"
                                                placeholder="Enter Courier Deadline" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">Hours</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('delivery_date_time') }}</span>
                                        </small>
                                    </div>

                                    {{-- Pay on Portal Fields --}}
                                @elseif($instrumentType == '6')
                                    @php
                                        $pop = $emd?->emdPayOnPortals->first();
                                    @endphp
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="purpose">Purpose</label>
                                        <input type="text" name="purpose" id="purpose" class="form-control"
                                            value="{{ $pop?->purpose ?? old('purpose') }}" placeholder="Enter Purpose"
                                            required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="portal_name">Name of Portal</label>
                                        <input type="text" name="portal_name" id="portal_name" class="form-control"
                                            value="{{ $pop?->portal ?? old('portal_name') }}"
                                            placeholder="Enter Name of Portal" required>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('portal_name') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="netbanking_available">Netbanking available</label>
                                        <select name="netbanking_available" id="netbanking_available"
                                            class="form-control" required>
                                            <option value="">Select</option>
                                            <option {{ $pop?->is_netbanking == 'yes' ? 'selected' : '' }} value="yes">
                                                Yes</option>
                                            <option {{ $pop?->is_netbanking == 'no' ? 'selected' : '' }} value="no">No
                                            </option>
                                        </select>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('netbanking_available') }}</span>
                                        </small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="form-label" for="bank_debit_card">Yes Bank Debit card</label>
                                        <select name="bank_debit_card" id="bank_debit_card" class="form-control"
                                            required>
                                            <option value="">Select</option>
                                            <option {{ $pop?->is_debit == 'yes' ? 'selected' : '' }} value="yes">
                                                Yes
                                            </option>
                                            <option {{ $pop?->is_debit == 'yes' ? 'selected' : '' }} value="no">
                                                No
                                            </option>
                                        </select>
                                        <small class="text-muted">
                                            <span class="text-danger">{{ $errors->first('bank_debit_card') }}</span>
                                        </small>
                                    </div>
                                @endif

                                {{-- Common Amount Field --}}
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" step="any" name="amount" id="amount"
                                        class="form-control" value="{{ $emd?->tender?->tender_fees ?? old('amount') }}"
                                        placeholder="Enter Amount" required>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    </small>
                                </div>

                                <div class="col-md-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
