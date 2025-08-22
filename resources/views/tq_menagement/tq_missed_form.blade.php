@extends('layouts.app')
@section('page-title', 'TQ Missed Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ asset('/admin/tq_missed_form_post') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf

                            <input type="hidden" value="{{ $tender_id }}" name="tender_id">

                            <div class="row">

                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">Reason for Missing?<span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="text" class="form-control" name="reason_missing" id="input28"
                                            placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">Would Ensure Not Repeated <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="text" class="form-control" name="would_repeated" id="input28"
                                            placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <label for="input28" class="form-label">Improvements Needed TMS System <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="text" class="form-control" name="tms_system" id="input28"
                                            placeholder="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-5">
                                <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
