@extends('layouts.app')
@section('title', 'Tender Fee Create')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Pay on Portal - Tender Fees</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('tender-fees.pop.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="tender_name">Tender name</label>
                                    <input type="text" name="tender_name" id="tender_name" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="due_date_time">Due date and time</label>
                                    <input type="datetime-local" name="due_date_time" id="due_date_time"
                                        class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('due_date_time') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="purpose">Purpose</label>
                                    <input type="text" name="purpose" id="purpose" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="portal_name">
                                        Name of Portal
                                    </label>
                                    <input type="text" name="portal_name" id="portal_name" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('portal_name') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="netbanking_available">Netbanking available
                                    </label>
                                    <select name="netbanking_available" id="netbanking_available" class="form-control">
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('netbanking_available') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="bank_debit_card">Yes Bank Debit card
                                    </label>
                                    <select name="bank_debit_card" id="bank_debit_card" class="form-control">
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('bank_debit_card') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" step="any" name="amount" id="amount" class="form-control">
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
