@extends('layouts.app')
@section('page-title', 'Loan Closure Update')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/loancloseupdate_post') }}" enctype="multipart/form-data"
                            id="format-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            @csrf
                            <input type="text" class="form-control" value="{{ $loanclose->id }}" name="id" hidden
                                id="input35">
                            <div class="col-md-4 ">
                                <label for="input38" class=" col-form-label">Upload Bank NOC Document<span
                                        style="color:#d2322d"> *</span></label>
                                <div class="d-flex"> <input type="file" name="banknoc_document" class="form-control" id="input38" required>
                                    <a class="ms-2 mt-1" href="{{ asset('upload/loanclose/' . $loanclose->banknoc_document) }}" download data-caption="">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Upload Closure of Any Charge Created On
                                    MCA<span style="color:#d2322d"> *</span></label>
                                <div class="d-flex">
                                    <input type="file" name="closurecreated_mca" class="form-control" id="input38" required>
                                    <a class="ms-2 mt-1"
                                        href="{{ asset('upload/loanclose/' . $loanclose->closurecreated_mca) }}" download data-caption=""> 
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">

                                    <button type="submit" class="btn btn-primary px-4">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
