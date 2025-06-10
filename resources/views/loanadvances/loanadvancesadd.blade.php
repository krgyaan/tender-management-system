@extends('layouts.app')
@section('page-title', 'Add Loan & Advances Details')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/loanadvancescreate') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <div class="col-md-4">
                                <label for="partyname" class=" col-form-label">Loan Party Name<span style="color:#d2322d">
                                        *</span></label>

                                <select name="loanparty_name" id="partyname" class="form-control" required>
                                    <option value="0">Select Option</option>
                                    @foreach ($loanpartname as $key => $loanpartnameData)
                                        <option value="{{ $loanpartnameData->id }}">{{ $loanpartnameData->loanparty_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('loanparty_name'))
                                    <span class="text-danger">
                                        @error('loanparty_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="bank" class=" col-form-label">Bank Name<span style="color:#d2322d">
                                        *</span></label>

                                <input type="text" class="form-control" name="bank_name" id="bank" required>
                                @if ($errors->has('bank_name'))
                                    <span class="text-danger">
                                        @error('bank_name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="typeof_loan" class=" col-form-label">Type of loan<span style="color:#d2322d">
                                        *</span> </label>
                                <select name="typeof_loan" id="typeof_loan" class="form-control" required>
                                    <option value="">Selete Type</option>
                                    <option value="Team Loan">Team Loan</option>
                                    <option value="CC/Limit">CC/Limit</option>
                                </select>
                                @if ($errors->has('typeof_loan'))
                                    <span class="text-danger">
                                        @error('typeof_loan')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-4">
                                <label for="loamamt" class=" col-form-label">Loan Amount<span style="color:#d2322d">
                                        *</span></label>

                                <input type="text" class="form-control" name="loanamount" id="loamamt" required>
                                @if ($errors->has('loanamount'))
                                    <span class="text-danger">
                                        @error('loanamount')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="sanction" class=" col-form-label">Sanction Letter Date<span
                                        style="color:#d2322d"> *</span></label>

                                <input type="date" class="form-control" name="sanctionletter_date" id="sanction"
                                    required>
                                @if ($errors->has('sanctionletter_date'))
                                    <span class="text-danger">
                                        @error('sanctionletter_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="emipayment_date" class="col-form-label">EMI Payment Date<span
                                        style="color:#d2322d">
                                        *</span></label>

                                <input type="date" name="emipayment_date" class="form-control" id="emipayment_date">
                                @if ($errors->has('emipayment_date'))
                                    <span class="text-danger">
                                        @error('emipayment_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="last_emi" class="col-form-label">Last EMI Date<span style="color:#d2322d">
                                        *</span></label>

                                <input type="date" name="lastemi_date" class="form-control" id="last_emi">
                                @if ($errors->has('lastemi_date'))
                                    <span class="text-danger">
                                        @error('lastemi_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-4 ">
                                <label for="sanction_letter" class=" col-form-label">Upload Sanction Letter:</label>
                                <input type="file" name="sanction_letter" class="form-control" id="sanction_letter">
                            </div>

                            <div class="col-md-4">
                                <label for="bank_loan" class=" col-form-label">Upload Bank Loan Schedule<span
                                        style="color:#d2322d"> *</span></label>

                                <input type="file" name="bankloan_schedule" class="form-control" id="bank_loan" required>
                                @if ($errors->has('bankloan_schedule'))
                                    <span class="text-danger">
                                        @error('bankloan_schedule')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="loan_schedule" class=" col-form-label">Upload Loan Schedule(Excel Only)</label>
                                <input type="file" name="loan_schedule" class="form-control" id="loan_schedule">
                                @if ($errors->has('loan_schedule'))
                                    <span class="text-danger">
                                        @error('loan_schedule')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="mca_charge" class=" col-form-label">Charge created on MCA Website:</label>
                                <select name="chargemca_website" id="mca_charge" class="form-control" required>
                                    <option>Select Option</option>
                                    <option>YES</option>
                                    <option>NO</option>
                                </select>
                                @if ($errors->has('chargemca_website'))
                                    <span class="text-danger">
                                        @error('chargemca_website')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="tds" class=" col-form-label">TDS to be deducted on interest:</label>
                                <select name="tdstobedeductedon_interest" id="tds" class="form-control" required>
                                    <option>Select Option</option>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            FilePond.registerPlugin(FilePondPluginFileValidateType);
            
            $('#sanction_letter, #bank_loan').filepond({
                credits: false,
                storeAsFile: true,
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                    'application/vnd.ms-excel': '.xls',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#loan_schedule').filepond({
                credits: false,
                storeAsFile: true,
                acceptedFileTypes: [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ],
                fileValidateTypeLabelExpectedTypesMap: {
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
        });
    </script>
@endpush
