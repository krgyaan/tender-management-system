@extends('layouts.app')
@section('page-title', 'Edit Loan & Advances Details')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/loanadvancesedit') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            <input type="text" class="form-control" value="{{ $loanadvances->id }}" name="id" hidden
                                id="input35">
                            @csrf

                            <div class="col-md-4">
                                <label for="" class=" col-form-label">Loan Party Name<span style="color:#d2322d">
                                        *</span></label>

                                <select name="loanparty_name" id="" class="form-control" required>
                                    @foreach ($loanpartname as $loanpartnameData)
                                        <option value="{{ $loanpartnameData->id }}"
                                            @if ($loanadvances->loanparty_name == $loanpartnameData->id) selected @endif>
                                            {{ $loanpartnameData->loanparty_name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-4">
                                <label for="input35" class=" col-form-label">Bank Name<span style="color:#d2322d">
                                        *</span></label>

                                <input type="text" class="form-control" value="{{ $loanadvances->bank_name }}"
                                    name="bank_name" id="input35">
                            </div>
                            <div class="col-md-4">
                                <label for="input39" class=" col-form-label">Type of loan<span style="color:#d2322d">
                                        *</span> </label>
                                <select name="typeof_loan" id="" class="form-control" required>
                                    <option selected="">{{ $loanadvances->typeof_loan }}</option>
                                    <option value="Team Loan">Team Loan</option>
                                    <option value="CC/Limit">CC/Limit</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="input36" class=" col-form-label">Lone Amount<span style="color:#d2322d">
                                        *</span></label>

                                <input type="text" value="{{ $loanadvances->loanamount }}" class="form-control"
                                    name="loanamount" id="input36"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">

                            </div>
                            <div class="col-md-4">
                                <label for="input37" class=" col-form-label">Sanction Letter Date<span
                                        style="color:#d2322d"> *</span></label>

                                <input type="date" value="{{ $loanadvances->sanctionletter_date }}" class="form-control"
                                    name="sanctionletter_date" id="input37" required>

                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">EMI Payment Date<span style="color:#d2322d">
                                        *</span></label>

                                <input type="date" value="{{ $loanadvances->emipayment_date }}" name="emipayment_date"
                                    class="form-control" id="input38" required>

                                @if ($errors->has('emipayment_date'))
                                    <span class="text-danger">
                                        @error('emipayment_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Last EMI Date<span style="color:#d2322d">
                                        *</span></label>
                                <input type="date" value="{{ $loanadvances->lastemi_date }}" name="lastemi_date"
                                    class="form-control" id="input38" required>
                                @if ($errors->has('lastemi_date'))
                                    <span class="text-danger">
                                        @error('lastemi_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4 ">
                                <label for="input38" class=" col-form-label">Upload Sanction Letter</label>
                                <div class="d-flex">
                                    <input type="file" name="sanction_letter" class="form-control" id="input38">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Upload Bank Loan Schedule <span
                                        style="color:#d2322d"> *</span></label>

                                <div class="d-flex">
                                    <input type="file" name="bankloan_schedule" class="form-control" id="input38">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Upload Loan Schedule(Excel Only)</label>

                                <div class="d-flex">
                                    <input type="file" name="loan_schedule" class="form-control" id="input38">
                                    @if ($errors->has('loan_schedule'))
                                        <span class="text-danger">
                                            @error('loan_schedule')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="input39" class=" col-form-label">Charge created on MCA Website:</label>
                                <select name="chargemca_website" id="" class="form-control" required>
                                    <option selected="">{{ $loanadvances->chargemca_website }}</option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="input39" class=" col-form-label">TDS to be deducted on interest:</label>
                                <select name="tdstobedeductedon_interest" id="" class="form-control" required>
                                    <option selected="">{{ $loanadvances->tdstobedeductedon_interest }}</option>
                                    <option value="Yes">YES</option>
                                    <option value="No">NO</option>
                                </select>
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
