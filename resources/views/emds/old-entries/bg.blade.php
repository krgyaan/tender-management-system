@extends('layouts.app')
@section('page-title', 'Old Bank Guarantee Entry')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="tender_no">Tender No./Wo. No.</label>
                                    <input type="text" name="tender_no" id="tender_no" class="form-control">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="project_name">Tender/Project Name</label>
                                    @php
                                        $projects = \App\Models\Project::latest()->get()->pluck('project_name');
                                    @endphp
                                    <select class="form-control" name="project_name" id="project_name">
                                        <option value="">-- Select Project --</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project }}">
                                                {{ $project }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_purpose">Purpose of BG</label>
                                    <select name="bg_purpose" id="bg_purpose" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="advance">Advance Payment</option>
                                        <option value="deposit">Security Bond/ Deposit</option>
                                        <option value="bid">Bid Bond</option>
                                        <option value="performance">Performance</option>
                                        <option value="financial">Financial</option>
                                        <option value="counter">Counter Guarantee</option>
                                    </select>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_favour">BG in Favour of</label>
                                    <input type="text" name="bg_favour" id="bg_favour" class="form-control" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_address">BG Address</label>
                                    <textarea name="bg_address" id="bg_address" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_expiry">BG Expiry Date</label>
                                    <input type="date" name="bg_expiry" id="bg_expiry" class="form-control" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_claim">BG Claim Period</label>
                                    <input type="date" name="bg_claim" id="bg_claim" class="form-control" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_amt">BG Amount</label>
                                    <input type="number" name="bg_amt" id="bg_amt" class="form-control" step="0.01"
                                        min="0" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_bank">BG Bank</label>
                                    <select name="bg_bank" id="bg_bank" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="HDFC_0026">HDFC_0026</option>
                                        <option value="YESBANK_2011">YESBANK_2011</option>
                                        <option value="YESBANK_0771">YESBANK_0771</option>
                                        <option value="BGLIMIT_0771">BG LIMIT</option>
                                        <option value="PNB_6011">PNB_6011</option>
                                        <option value="SBI">SBI</option>
                                    </select>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_stamp">BG Stamp Paper Value</label>
                                    <input type="number" name="bg_stamp" id="bg_stamp" class="form-control" step="0.01"
                                        min="0" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_po">PO/Tender/Request Letter Upload (If
                                        Avaialble)</label>
                                    <input type="file" name="bg_po" id="bg_po" class="form-control"
                                        accept=".pdf,.doc,.docx,image/*">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label">Client Email IDs (If Avaialble)</label>
                                    <div class="input-group mb-2">
                                        <input type="email" name="bg_client_user" class="form-control"
                                            placeholder="User Email">
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="email" name="bg_client_cp" class="form-control"
                                            placeholder="C&P Email">
                                    </div>
                                    <div class="input-group">
                                        <input type="email" name="bg_client_fin" class="form-control"
                                            placeholder="Finance Email">
                                    </div>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label">Client Bank Details (If Avaialble)</label>
                                    <div class="input-group mb-2">
                                        <input type="text" name="bg_bank_name" class="form-control"
                                            placeholder="Bank Account Name">
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="number" name="bg_bank_acc" class="form-control"
                                            placeholder="Account Number">
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="bg_bank_ifsc" class="form-control"
                                            placeholder="IFSC Code">
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_no">BG Number</label>
                                    <input type="text" name="bg_no" id="bg_no" class="form-control" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_date">BG Creation Date</label>
                                    <input type="date" name="bg_date" id="bg_date" class="form-control" required>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_soft_copy">Upload Soft copy of the BG</label>
                                    <input type="file" name="bg_soft_copy" id="bg_soft_copy" class="form-control"
                                        accept=".pdf,.doc,.docx,image/*">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="sfms">SFMS</label>
                                    <input type="file" name="sfms" id="sfms" class="form-control"
                                        accept=".pdf,.doc,.docx,image/*">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_per">FDR %</label>
                                    <div class="input-group">
                                        <select name="fdr_per" id="fdr_per" class="form-control">
                                            <option value="">Select FDR Percentage</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="100">100</option>
                                        </select>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_copy">FDR Copy</label>
                                    <input type="file" name="fdr_copy" id="fdr_copy" class="form-control"
                                        accept=".pdf,.doc,.docx,image/*">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_amt">FDR Amount</label>
                                    <input type="number" step="any" min="0" name="fdr_amt" id="fdr_amt"
                                        class="form-control">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_no">FDR Number</label>
                                    <input type="text" name="fdr_no" id="fdr_no" class="form-control">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_validity">FDR Validity</label>
                                    <input type="date" name="fdr_validity" id="fdr_validity" class="form-control">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="fdr_roi">FDR ROI%</label>
                                    <div class="input-group">
                                        <input type="number" name="fdr_roi" id="fdr_roi" class="form-control"
                                            step="any" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_charges">BG Charges Deducted</label>
                                    <input type="number" name="bg_charges" id="bg_charges" class="form-control"
                                        step="0.01" min="0">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="sfms_charges">SFMS Charges Deducted</label>
                                    <input type="number" name="sfms_charges" id="sfms_charges" class="form-control"
                                        step="0.01" min="0">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="stamp_charges">Stamp Paper Charges Deducted</label>
                                    <input type="number" name="stamp_charges" id="stamp_charges" class="form-control"
                                        step="0.01" min="0">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="other_charges">Other Charges Deducted</label>
                                    <input type="number" name="other_charges" id="other_charges" class="form-control"
                                        step="0.01" min="0">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="remarks">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
            $('#project_name').select2({
                placeholder: 'Select Project',
                allowClear: true,
                width: '100%',
                height: 38,
            });
        });
    </script>
@endpush
