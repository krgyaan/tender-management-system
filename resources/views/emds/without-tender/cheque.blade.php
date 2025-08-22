@extends('layouts.app')
@section('page-title', 'Other Than Tender Cheque')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="cheque_needed_in">Cheque Needed in</label>
                                    <select name="cheque_needed_in" id="cheque_needed_in" class="form-control" required>
                                        <option value="">Select</option>
                                        <option {{ old('cheque_needed_in') == '3' ? 'selected' : '' }}
                                            value="3">3 Hours</option>
                                        <option {{ old('cheque_needed_in') == '6' ? 'selected' : '' }}
                                            value="6">6 Hours</option>
                                        <option {{ old('cheque_needed_in') == '12' ? 'selected' : '' }}
                                            value="12">12 Hours</option>
                                        <option {{ old('cheque_needed_in') == '24' ? 'selected' : '' }}
                                            value="24">24 Hours</option>
                                    </select>
                                    <small class="text-muted">
                                        <span
                                            class="text-danger">{{ $errors->first('cheque_needed_in') }}</span>
                                    </small>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="purpose">Purpose of the Cheque</label>
                                    <select name="purpose" id="purpose" class="form-control" required>
                                        <option value="">Select</option>
                                        <option {{ old('purpose') == 'Payable' ? 'selected' : '' }}
                                            value="Payable">Payable</option>
                                        <option {{ old('purpose') == 'Security' ? 'selected' : '' }}
                                            value="Security">Security</option>
                                        <option {{ old('purpose') == 'DD' ? 'selected' : '' }}
                                            value="DD">DD</option>
                                        <option {{ old('purpose') == 'FDR' ? 'selected' : '' }}
                                            value="FDR">FDR</option>
                                    </select>
                                    <small class="text-muted">
                                        <span
                                            class="text-danger">{{ $errors->first('purpose') }}</span>
                                    </small>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="cheque_favour">Cheque in Favour of</label>
                                    <input type="text" name="cheque_favour" id="cheque_favour" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="cheque_date">Cheque Date</label>
                                    <input type="date" name="cheque_date" id="cheque_date" class="form-control" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="cheque_account">Account to be debited from</label>
                                    <select name="cheque_account" id="cheque_account" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="HDFC_0026">HDFC_0026</option>
                                        <option value="YESBANK_2011">YESBANK_2011</option>
                                        <option value="YESBANK_0771">YESBANK_0771</option>
                                        <option value="PNB_6011">PNB_6011</option>
                                        <option value="SBI">SBI</option>
                                    </select>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" step="0.01"
                                        min="0" required>
                                </div>

                                <div class="col-md-12">
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
