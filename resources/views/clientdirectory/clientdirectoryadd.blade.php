@extends('layouts.app')
@section('page-title', 'Add Client Directory')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/clientdirectorycreate') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            @csrf
                            <div class="col-md-4">
                                <label for="input25" class="form-label">Organization</label>
                                <input type="text" class="form-control" name="organization" id="input25"
                                    placeholder="organization" required>
                            </div>
                            <div class="col-md-4">
                                <label for="input26" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="input26" placeholder="Name"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="input28" class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" id="input28"
                                    placeholder="designattion">
                            </div>

                            <div class="col-md-4">
                                <label for="input28" class="form-label">Phone no.</label>
                                <input type="number" class="form-control" name="phone_no" id="input28"
                                    placeholder="Phone no." maxlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="input27" class="form-label">Email ID</label>
                                <input type="email" class="form-control" name="email" id="input27" placeholder="Email"
                                    oninput="this.value = this.value.replace(/\s+/g, '')" required>
                            </div>
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
    </section>
@endsection
