@extends('layouts.app')
@section('page-title', 'TQ Replied Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ asset('/admin/tq_replied_form_post') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" value="{{ $tender_id }}" name="tender_id">
                            <div class="row">

                                <div class="col-md-6 pt-3">
                                    <label for="input28" class="form-label">TQ Submission time <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="date" class="form-control" name="date" id="input28"
                                            placeholder="Date" required>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-3">
                                    <label for="input28" class="form-label">TQ Submission time <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="time" class="form-control" name="time" id="input28"
                                            placeholder="Date" required>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-3">
                                    <label for="input28" class="form-label">Uplode TQ document <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="file" class="form-control" name="tq_img" id="input28"
                                            placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-3">
                                    <label for="input28" class="form-label">Uplode Proof of Submission <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">

                                        <input type="file" class="form-control" name="proof_submission" id="input28"
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
