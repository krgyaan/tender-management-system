<div class="row" id="tender_fee_fields">
    @include('partials.div-separator', [
        'text' => 'Tender Fee Payment Details',
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
        <label class="form-label" for="tf_portal">Name of Portal/Website</label>
        <input type="text" name="tf_portal" id="tf_portal" class="form-control" placeholder="Enter Portal Name"
            required>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_is_netbanking">Net Banking Available</label>
        <select name="tf_is_netbanking" id="tf_is_netbanking" class="form-control" required>
            <option value="">-- Choose --</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_is_debit">Yes Bank Debit Card
            Allowed</label>
        <select name="tf_is_debit" id="tf_is_debit" class="form-control" required>
            <option value="">-- Choose --</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>
</div>
