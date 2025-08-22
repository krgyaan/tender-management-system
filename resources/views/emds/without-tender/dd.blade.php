@extends('layouts.app')
@section('page-title', 'Other Than Tender Demand Draft')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="project_name">Project Name</label>
                                    <input type="text" name="project_name" id="project_name" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="dd_needed_in">DD needed in</label>
                                    <input type="text" name="dd_needed_in" id="dd_needed_in" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="purpose">Purpose of DD</label>
                                    <input type="text" name="purpose" id="purpose" class="form-control" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="dd_favour">DD in favour of</label>
                                    <input type="text" name="dd_favour" id="dd_favour" class="form-control" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="dd_payable_at">DD Payable at</label>
                                    <input type="text" name="dd_payable_at" id="dd_payable_at" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="amount">DD Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" step="0.01"
                                        min="0" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="courier_address">Courier Address</label>
                                    <textarea name="courier_address" id="courier_address" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="form-label" for="courier_delivery_time">Courier Delivery Time</label>
                                    <div class="input-group">
                                        <select name="courier_delivery_time" id="courier_delivery_time" class="form-control"
                                            required>
                                            <option value="">Select Days</option>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <span class="input-group-text">Days</span>
                                    </div>
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
