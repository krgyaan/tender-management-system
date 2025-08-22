@extends('layouts.app')
@section('page-title', 'Other Than Tender Bank Guarantee')
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
                                    <label class="form-label" for="tender_no">WO. No.</label>
                                    <input type="text" name="tender_no" id="tender_no" class="form-control" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="project_name">Project Name</label>
                                    <input type="text" name="project_name" id="project_name" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_needed_in">BG Needed In</label>
                                    <input type="text" name="bg_needed_in" id="bg_needed_in" class="form-control"
                                        required>
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
                                    <label class="form-label" for="bg_format_upload">BG Format Upload TE</label>
                                    <input type="file" name="bg_format_upload" id="bg_format_upload"
                                        class="form-control" accept=".pdf,.doc,.docx,image/*">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_po">PO/Tender/Request Letter Upload</label>
                                    <input type="file" name="bg_po" id="bg_po" class="form-control"
                                        accept=".pdf,.doc,.docx,image/*">
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label">Client Email IDs</label>
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
                                    <label class="form-label">Client Bank Details</label>
                                    <div class="input-group mb-2">
                                        <input type="text" name="bg_bank_name" class="form-control"
                                            placeholder="Bank Account Name">
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" name="bg_bank_acc" class="form-control"
                                            placeholder="Account Number">
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="bg_bank_ifsc" class="form-control"
                                            placeholder="IFSC Code">
                                    </div>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_courier_addr">BG Courier Address</label>
                                    <textarea name="bg_courier_addr" id="bg_courier_addr" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="col-md-4 form-group mb-3">
                                    <label class="form-label" for="bg_courier_deadline">Courier Delivery Time</label>
                                    <div class="input-group">
                                        <input type="number" name="bg_courier_deadline" id="bg_courier_deadline"
                                            class="form-control" placeholder="Days" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Days</span>
                                        </div>
                                    </div>
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
