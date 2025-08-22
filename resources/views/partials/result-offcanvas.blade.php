<div class="offcanvas offcanvas-start" tabindex="-1" id="{{ $offcanvasId }}" aria-labelledby="{{ $offcanvasId }}-label"
    data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="{{ $offcanvasId }}-label">Update EMD Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="" method="POST">
            @csrf
            <div class="mb-3">
                <input type="hidden" name="emd_id" value="{{ $emdId }}">

                <label for="emd_status" class="form-label">EMD Status</label>
                <select class="form-select" id="emd_status" name="emd_status" required>
                    <option value="" disabled selected>Select EMD Status</option>
                    <option value="2">Initiate Followup</option>
                    <option value="4">Settled with Project Account</option>
                </select>
            </div>
            <div class="followup">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="org_name" class="form-label">Organisation Name</label>
                        <input type="text" name="org_name" class="form-control" id="org_name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="form-label">Contact details:</label>
                            <a href="javascript:void(0)" class="addPopFollowup">Add Person</a>
                        </div>
                        <div class="row" id="emd-followups">
                            <div class="col-md-4 form-group">
                                <input type="text" name="fp[0][name]" class="form-control" id="name"
                                    placeholder="Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <input type="number" name="fp[0][phone]" class="form-control" id="phone"
                                    placeholder="Phone">
                            </div>
                            <div class="col-md-4 form-group">
                                <input type="email" name="fp[0][email]" class="form-control" id="email"
                                    placeholder="Email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="start_date">Followup Start From:</label>
                        <input type="date" name="start_date" class="form-control" id="start_date">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        @php
                            $ferq = [
                                '1' => 'Daily',
                                '2' => 'Alternate Days',
                                '3' => '2 times a day',
                                '4' => 'Weekly (every Mon)',
                                '5' => 'Twice a Week (every Mon & Thu)',
                                '6' => 'Stop',
                            ];
                        @endphp
                        <label class="form-label" for="frequency">Followup Frequency:</label>
                        <select name="frequency" id="frequency" class="form-control">
                            <option value="">choose</option>
                            @foreach ($ferq as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12 stop" style="display: none">
                    <div class="form-group">
                        <label class="form-label" for="stop_reason">Why Stop:</label>
                        <select name="stop_reason" class="form-control" id="stop_reason">
                            <option value="">choose</option>
                            <option value="1">
                                The person is getting angry/or has requested to stop
                            </option>
                            <option value="2">Followup Objective achieved</option>
                            <option value="3">External Followup Initiated</option>
                            <option value="4">Remarks</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 stop_proof" style="display: none">
                    <div class="form-group">
                        <label class="form-label">Please give proof:</label>
                        <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                        <input type="file" name="proof_img" class="form-control mt-2" id="proof_img">
                    </div>
                </div>
                <div class="col-md-12 stop_rem" style="display: none">
                    <div class="form-group">
                        <label class="form-label">Write Remarks:</label>
                        <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>
