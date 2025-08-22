@extends('layouts.app')
@section('page-title', 'Edit Loan & Advances Details')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 mx-auto">
                @include('partials.messages')
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/loanadvancesedit') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            <input type="text" class="form-control" value="{{ $loanadvances->id }}" name="id" hidden
                                id="input35">
                            @csrf

                            <div class="col-md-4">
                                <label for="" class=" col-form-label">Loan Party Name</label>
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
                                <label for="loac_acc_no" class=" form-label">Loan Account Number</label>
                                <input type="text" class="form-control" name="loac_acc_no" id="loac_acc_no" value="{{ $loanadvances->loac_acc_no }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="input35" class=" col-form-label">Bank Name</label>
                                <input type="text" class="form-control" value="{{ $loanadvances->bank_name }}"
                                    name="bank_name" id="input35">
                            </div>
                            <div class="col-md-4">
                                <label for="input39" class=" col-form-label">Type of loan </label>
                                <select name="typeof_loan" id="" class="form-control" required>
                                    <option selected="">{{ $loanadvances->typeof_loan }}</option>
                                    <option value="Team Loan">Team Loan</option>
                                    <option value="CC/Limit">CC/Limit</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="input36" class=" col-form-label">Lone Amount</label>

                                <input type="text" value="{{ $loanadvances->loanamount }}" class="form-control"
                                    name="loanamount" id="input36"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">

                            </div>
                            <div class="col-md-4">
                                <label for="input37" class=" col-form-label">Sanction Letter Date(PDF only)</label>

                                <input type="date" value="{{ $loanadvances->sanctionletter_date }}" class="form-control"
                                    name="sanctionletter_date" id="input37" required>

                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">EMI Payment Date</label>

                                <input type="date" value="{{ $loanadvances->emipayment_date }}" name="emipayment_date"
                                    class="form-control" id="input38" required>
                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Last EMI Date</label>
                                <input type="date" value="{{ $loanadvances->lastemi_date }}" name="lastemi_date"
                                    class="form-control" id="input38" required>
                            </div>
                            <div class="col-md-4 ">
                                <label for="input38" class=" col-form-label">Upload Sanction Letter</label>
                                <div class="d-flex">
                                    <input type="file" name="sanction_letter" class="form-control" id="input38">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="input38" class=" col-form-label">Upload Bank Loan Schedule (PDF only)</label>
                                <div class="d-flex">
                                    <input type="file" name="bankloan_schedule" class="form-control" id="input38"
                                        accept="application/pdf">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="loan_schedule" class="col-form-label">Upload Loan Schedule (Excel Only)</label>
                                <div class="mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="schedule_type" id="file_upload"
                                            value="file" {{ $loanadvances->loan_schedule ? 'checked' : '' }}>
                                        <label class="form-check-label" for="file_upload">Upload File</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="schedule_type"
                                            id="sheet_url" value="url"
                                            {{ $loanadvances->loan_schedule_url ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sheet_url">Google Sheet URL</label>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <input type="file" name="loan_schedule" class="form-control" id="loan_schedule"
                                        accept=".xls,.xlsx"
                                        style="{{ $loanadvances->loan_schedule_url ? 'display:none;' : '' }}">
                                    <input type="url" name="loan_schedule_url" id="loan_schedule_url"
                                        class="form-control" placeholder="Enter Loan Schedule Sheet URL"
                                        value="{{ $loanadvances->loan_schedule_url }}"
                                        style="{{ $loanadvances->loan_schedule ? 'display:none;' : '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="input39" class=" col-form-label">Charge created on MCA Website:</label>
                                <select name="chargemca_website" id="" class="form-control" required>
                                    <option selected="">{{ $loanadvances->chargemca_website }}</option>
                                    <option value="Yes">YES</option>
                                    <option value="No">NO</option>
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
            
            $('input[name="schedule_type"]').on('change', function() {
                if ($(this).val() === 'file') {
                    $('#loan_schedule').show();
                    $('#loan_schedule_url').hide();
                } else {
                    $('#loan_schedule').hide();
                    $('#loan_schedule_url').show();
                }
            });
        });
    </script>
@endpush