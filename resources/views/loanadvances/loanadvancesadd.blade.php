@extends('layouts.app')
@section('page-title', 'Add Loan & Advances Details')
@section('content')
    <section>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('loanadvances') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <form method="post" action="{{ asset('/admin/loanadvancescreate') }}" enctype="multipart/form-data"
                    id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="col-md-4">
                        <label for="partyname" class=" form-label">Loan Party Name</label>
                        <select name="loanparty_name" id="partyname" class="form-control" required>
                            <option value="">Select Option</option>
                            @foreach ($loanpartname as $key => $loanpartnameData)
                                <option value="{{ $loanpartnameData->id }}">
                                    {{ $loanpartnameData->loanparty_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="loac_acc_no" class=" form-label">Loan Account Number</label>
                        <input type="text" class="form-control" name="loac_acc_no" id="loac_acc_no" required>
                    </div>
                    <div class="col-md-4">
                        <label for="bank" class=" form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bank_name" id="bank" required>
                    </div>
                    <div class="col-md-4">
                        <label for="typeof_loan" class=" form-label">Type of loan </label>
                        <select name="typeof_loan" id="typeof_loan" class="form-control" required>
                            <option value="">Selete Type</option>
                            <option value="Team Loan">Team Loan</option>
                            <option value="CC/Limit">CC/Limit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="loamamt" class=" form-label">Loan Amount</label>
                        <input type="number" step="any" class="form-control" name="loanamount" id="loamamt" required>
                    </div>
                    <div class="col-md-4">
                        <label for="sanction" class=" form-label">Sanction Letter Date</label>
                        <input type="date" class="form-control" name="sanctionletter_date" id="sanction" required>
                    </div>
                    <div class="col-md-4">
                        <label for="emipayment_date" class="form-label">EMI Payment Date</label>
                        <input type="date" name="emipayment_date" class="form-control" id="emipayment_date">
                    </div>
                    <div class="col-md-4">
                        <label for="last_emi" class="form-label">Last EMI Date</label>
                        <input type="date" name="lastemi_date" class="form-control" id="last_emi">
                    </div>

                    <div class="col-md-4 ">
                        <label for="sanction_letter" class=" form-label">Upload Sanction Letter</label>
                        <input type="file" name="sanction_letter" class="form-control" id="sanction_letter">
                    </div>

                    <div class="col-md-4">
                        <label for="bank_loan" class=" form-label">Upload Bank Loan Schedule (PDF only)</label>
                        <input type="file" name="bankloan_schedule" class="form-control" id="bank_loan"
                            accept="application/pdf" required>
                    </div>

                    <div class="col-md-4">
                        <label for="loan_schedule" class=" form-label">Upload Loan Schedule (Excel Only)</label>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="schedule_type" id="file_upload"
                                    value="file" checked>
                                <label class="form-check-label" for="file_upload">Upload File</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="schedule_type" id="sheet_url"
                                    value="url">
                                <label class="form-check-label" for="sheet_url">Google Sheet URL</label>
                            </div>
                        </div>
                        <input type="file" name="loan_schedule" class="form-control" id="loan_schedule"
                            accept=".xls,.xlsx">
                        <input type="url" name="loan_schedule_url" id="loan_schedule_url" class="form-control"
                            placeholder="Enter Loan Schedule Sheet URL" style="display:none;">
                    </div>
                    <div class="col-md-4">
                        <label for="mca_charge" class=" form-label">Charge created on MCA Website:</label>
                        <select name="chargemca_website" id="mca_charge" class="form-control" required>
                            <option>Select Option</option>
                            <option value="Yes">YES</option>
                            <option value="No">NO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tds" class="form-label">TDS to be deducted on interest:</label>
                        <select name="tdstobedeductedon_interest" id="tds" class="form-control" required>
                            <option>Select Option</option>
                            <option value="Yes">YES</option>
                            <option value="No">NO</option>
                        </select>
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle between file input and URL input for loan schedule
            $('input[name="schedule_type"]').on('change', function() {
                if ($(this).val() === 'file') {
                    $('#loan_schedule').show();
                    $('#loan_schedule_url').hide();
                } else {
                    $('#loan_schedule').hide();
                    $('#loan_schedule_url').show();
                }
            });

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
