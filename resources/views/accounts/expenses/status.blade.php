@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('fixed-expenses.status.update', $fixedExpense) }}">
                            @csrf
                            @method('PUT')
                            @include('partials.messages')
                            <div class="form-group row" id="amount-field">
                                <label for="amount" class="col-md-4 col-form-label text-md-right">Amount</label>

                                <div class="col-md-6">
                                    <input id="amount" type="number" step="0.01"
                                        class="form-control @error('amount') is-invalid @enderror" name="amount"
                                        value="{{ old('amount', $fixedExpense->amount) }}">

                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>

                                <div class="col-md-6">
                                    <input id="status" type="text"
                                        class="form-control @error('status') is-invalid @enderror" name="status"
                                        value="{{ old('status', $fixedExpense->status) }}" required>

                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="utr_message" class="col-md-4 col-form-label text-md-right">UTR Message</label>

                                <div class="col-md-6">
                                    <textarea id="utr_message" class="form-control @error('utr_message') is-invalid @enderror" name="utr_message">{{ old('utr_message', $fixedExpense->utr_message) }}</textarea>

                                    @error('utr_message')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="payment_datetime" class="col-md-4 col-form-label text-md-right">Payment
                                    Date/Time</label>

                                <div class="col-md-6">
                                    <input id="payment_datetime" type="datetime-local"
                                        class="form-control @error('payment_datetime') is-invalid @enderror"
                                        name="payment_datetime"
                                        value="{{ old('payment_datetime', $fixedExpense->payment_datetime ? \Carbon\Carbon::parse($fixedExpense->payment_datetime)->format('Y-m-d\TH:i') : '') }}">

                                    @error('payment_datetime')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update Status
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
