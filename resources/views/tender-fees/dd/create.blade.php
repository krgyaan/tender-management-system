@extends('layouts.app')
@section('title', 'Tender Fee Create')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Demand Draft - Tender Fees</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('tender-fees.dd.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="tender_name">Tender Name</label>
                                    <input type="text" name="tender_name" id="tender_name" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('tender_name') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_needed_in">DD needed in</label>
                                    <input type="text" name="dd_needed_in" id="dd_needed_in" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('dd_needed_in') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="purpose_of_dd">Purpose of DD</label>
                                    <input type="text" name="purpose_of_dd" id="purpose_of_dd" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('purpose_of_dd') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="in_favour_of">DD in favour of</label>
                                    <input type="text" name="in_favour_of" id="in_favour_of" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('in_favour_of') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_payable_at">DD Payable at</label>
                                    <input type="text" name="dd_payable_at" id="dd_payable_at" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('dd_payable_at') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="dd_amount">DD Amount</label>
                                    <input type="number" step="any" name="dd_amount" id="dd_amount"
                                        class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('dd_amount') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="courier_address">Courier Address</label>
                                    <input type="text" name="courier_address" id="courier_address" class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('courier_address') }}</span>
                                    </small>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="delivery_date_time">Courier Delivery Date
                                        Time</label>
                                    <input type="datetime-local" name="delivery_date_time" id="delivery_date_time"
                                        class="form-control">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('delivery_date_time') }}</span>
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
