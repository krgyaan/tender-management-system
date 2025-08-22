@extends('layouts.app')
@section('page-title', 'Rent Agreements')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('rent') }}" class="btn btn-outline-danger btn-sm">back</a>
                    </div>
                    <div class="card">
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('/admin/rent_update') }}" enctype="multipart/form-data"
                                id="formatDistrict-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">

                                @csrf

                                <input type="text" value="{{ $rent_edit->id }}" name="id" class="form-control"
                                    id="">

                                <div class="col-md-6">
                                    <label for="first_party" class="form-label">First Party<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $rent_edit->first_party }}" name="first_party"
                                        class="form-control" id="" placeholder=" First Party"
                                        required>
                                                </div>
                                    <div class="col-md-6">
                                        <label for="second_party" class="form-label">Second Party<span class="text-danger">*</span></label>
                                        <input type="text" value="{{ $rent_edit->second_party }}" name="second_party" class="form-control" id="" placeholder=" Second Party" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="rent_amount" class="form-label">Rent Amount<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $rent_edit->rent_amount }}" name="rent_amount"
                                        class="form-control" id="" placeholder=" Rent Amount"
                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="security_deposit" class="form-label">Security Deposit<span class="text-danger">*</span></label>
                                                    <input type="text" value="{{ $rent_edit->security_deposit }}" name="security_deposit" class="form-control" id="" placeholder=" Security Deposit" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" value="{{ $rent_edit->start_date }}" name="start_date"
                                        class="form-control" id="" placeholder=" Start Date"
                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="end_date" class="form-label">End Date<span class="text-danger">*</span></label>
                                                    <input type="date" value="{{ $rent_edit->end_date }}" name="end_date" class="form-control" id="" placeholder=" End Date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="rent_increment_at_expiry" class="form-label">Rent Increment at Expiry<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $rent_edit->rent_increment_at_expiry }}"
                                        name="rent_increment_at_expiry" class="form-control" id=""
                                        placeholder=" Rent Increment at Expiry"
                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="image" class="form-label">Upload File<span class="text-danger">*</span></label>
                                                    <input type="file" name="image" class="form-control" id="" placeholder=" Rent Increment at Expiry">
                                    <img width="20%" src="/upload/finance/{{ $rent_edit->image }}" alt="">
                                </div>
                                <div class="col-md-12">
                                    <label for="image" class="form-label">Remarks<span
                                            class="text-danger">*</span></label>
                                </div>
                                <textarea name="remarks" id="" cols="80" rows="4" class="form-control">{{ $rent_edit->remarks }}</textarea>


                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">

                                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>





@endsection
