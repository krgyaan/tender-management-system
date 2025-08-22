<div class="row" id="tender_fee_fields">
    @include('partials.div-separator', [
        'text' => 'Tender Fee Bank Transfer Details',
    ])
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_amount">Amount</label>
        <input type="number" step="any" name="tf_amount" id="tf_amount" class="form-control"
            value="{{ $tender->tender_fees ?? old('tf_amount') }}" placeholder="Enter Amount" required>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_purpose">Purpose</label>
        <select name="tf_purpose" id="tf_purpose" class="form-control" required>
            <option value="Tender Fees" selected>Tender Fees</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_bt_acc_name">Account Name</label>
        <input type="text" name="tf_bt_acc_name" id="tf_bt_acc_name" class="form-control"
            placeholder="Enter Account Name" required>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_bt_acc">Account Number</label>
        <input type="number" name="tf_bt_acc" id="tf_bt_acc" class="form-control" placeholder="Enter Account Number"
            required>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_bt_ifsc">IFSC</label>
        <input type="text" name="tf_bt_ifsc" id="tf_bt_ifsc" class="form-control" placeholder="Enter IFSC" required>
    </div>
</div>
