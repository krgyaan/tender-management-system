@extends('layouts.app')
@section('title', 'Tender Fee Edit')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Bank Transfer - Tender Fees</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('tender-fees.bt.update', $btTenderFee->id) }}">
                            @csrf
                            <div class="row" id="bank_transfer">
                                <div class="col-md-4 form-group">
                                    <input type="hidden" name="tender_id"
                                        value="{{ isset($id) ? base64_decode($id) : '0' }}">
                                    <label class="form-label" for="purpose">Purpose</label>
                                    <input type="text" name="purpose" id="purpose" class="form-control"
                                        value="{{ $btTenderFee->purpose }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bt_acc_name">
                                        Account Name
                                    </label>
                                    <input type="text" name="account_name" id="account_name" class="form-control"
                                        value="{{ $btTenderFee->account_name }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('account_name') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="account_number">Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control"
                                        value="{{ $btTenderFee->account_number }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('account_number') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="ifsc">IFSC</label>
                                    <input type="text" name="ifsc" id="ifsc" class="form-control"
                                        value="{{ $btTenderFee->ifsc }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('ifsc') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" step="any" name="amount" id="amount" class="form-control"
                                        value="{{ $btTenderFee->amount }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-12 text-center">
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
