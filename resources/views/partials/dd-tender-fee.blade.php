<div class="row" id="tender_fee_fields">
    @include('partials.div-separator', ['text' => 'Tender Fees Details'])

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_amount">Amount</label>
        <input type="number" step="any" name="tf_amount" id="tf_amount" class="form-control"
            value="{{ $tender->tender_fees ?? old('tf_amount') }}" placeholder="Enter Amount" required>
        </small>
    </div>

    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_dd_needs">DD deliver by</label>
        <select name="tf_dd_needs" id="tf_dd_needs" class="form-control" required>
            <option value="">-- Choose --</option>
            <option value="due">Tender Due Date</option>
            <option value="24">24 Hours</option>
            <option value="36">36 Hours</option>
            <option value="48">48 Hours</option>
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_purpose_of_dd">Purpose of DD</label>
        <input type="text" name="tf_purpose_of_dd" id="tf_purpose_of_dd" class="form-control" value="Tender Fees"
            placeholder="Enter Purpose of DD" required>
    </div>
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_in_favour_of">DD in favour of</label>
        <input type="text" name="tf_in_favour_of" id="tf_in_favour_of" class="form-control"
            placeholder="Enter DD in Favour of" required>
    </div>
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_dd_payable_at">DD Payable at</label>
        <input type="text" name="tf_dd_payable_at" id="tf_dd_payable_at" class="form-control"
            placeholder="Enter DD Payable at" required>
    </div>
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_courier_address">Courier Address</label>
        <input type="text" name="tf_courier_address" id="tf_courier_address" class="form-control"
            placeholder="Enter Courier Address" required>
    </div>
    <div class="col-md-4 form-group">
        <label class="form-label" for="tf_courier_deadline">
            Time required for courier to reach destination:
        </label>
        <div class="input-group">
            <input type="number" name="tf_courier_deadline" id="tf_courier_deadline" class="form-control"
                placeholder="Enter Courier Deadline" required>
            <div class="input-group-append">
                <span class="input-group-text">Hours</span>
            </div>
        </div>
    </div>
</div>
