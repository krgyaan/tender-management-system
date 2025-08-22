@extends('layouts.app')
@section('page-title', 'Edit Client Directory ')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/clientdirectoryedit') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            <input type="text" value="{{ $clientdirectory->id }}" class="form-control" name="id"
                                id="input25" hidden placeholder="organization">
                            @csrf
                            <div class="col-md-4">
                                <label for="input25" class="form-label">organization</label>
                                <input type="text" value="{{ $clientdirectory->organization }}" class="form-control"
                                    name="organization" id="input25" placeholder="organization">
                            </div>
                            <div class="col-md-4">
                                <label for="input26" class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ $clientdirectory->name }}"
                                    name="name" id="input26" placeholder="Name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="input28" class="form-label">Designattion</label>
                                <input type="text" class="form-control" value="{{ $clientdirectory->designation }}"
                                    name="designation" id="input28" placeholder="designattion" required>
                            </div>
                            <div class="col-md-4">
                                <label for="input28" class="form-label">Phone no.</label>
                                <input type="number" class="form-control" value="{{ $clientdirectory->phone_no }}"
                                    name="phone_no" id="input28" placeholder="Phone no." maxlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    required>
                                @if ($errors->has('phone_no'))
                                    <span class="text-danger">
                                        @error('phone_no')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="input27" class="form-label">Email ID</label>
                                <input type="email" class="form-control" value="{{ $clientdirectory->email }}"
                                    name="email" id="input27" placeholder="Email"
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
    </section>
@endsection
