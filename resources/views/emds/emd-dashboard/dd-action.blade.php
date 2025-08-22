@extends('layouts.app')
@section('page-title', 'Demand Draft Actions')
@php
    $ferq = [
        '1' => 'Daily',
        '2' => 'Alternate Days',
        '3' => '2 times a day',
        '4' => 'Weekly (every Mon)',
        '5' => 'Twice a Week (every Mon & Thu)',
        '6' => 'Stop',
    ];
    $instrumentType = [
        '0' => 'NA',
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];
    $ddStatus = [
        1 => 'Accounts Form (DD)',
        2 => 'Initiate Followup',
        3 => 'Returned via courier',
        4 => 'Returned via Bank Transfer',
        5 => 'Settled with Project Account',
        6 => 'Send DD Cancellation Request',
        7 => 'DD cancelled at Branch',
    ];
    $couriers = App\Models\CourierDashboard::all();
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="" method="post" enctype="multipart/form-data" id="updateStatusForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="action" class="form-label">Choose What to do:</label>
                                        <select class="form-control" id="ddaction" name="action">
                                            <option value="">Select Action</option>
                                            <option {{ $dd->action == '1' ? 'selected' : '' }} value="1">Accounts Form
                                            </option>
                                            <option {{ $dd->action == '2' ? 'selected' : '' }} value="2">Initiate
                                                Followup</option>
                                            <option {{ $dd->action == '3' ? 'selected' : '' }} value="3">Returned via
                                                courier</option>
                                            <option {{ $dd->action == '4' ? 'selected' : '' }} value="4">Returned via
                                                Bank Transfer</option>
                                            <option {{ $dd->action == '5' ? 'selected' : '' }} value="5">Settled with
                                                Project Account</option>
                                            <option {{ $dd->action == '6' ? 'selected' : '' }} value="6">Send DD
                                                Cancellation Request</option>
                                            <option {{ $dd->action == '7' ? 'selected' : '' }} value="7">DD cancelled
                                                at Branch</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $dd->action == 1 ? 'flex' : 'none' }}" id="ddaccount">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="status">DD Request:</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">-- Select --</option>
                                        <option value="Accepted" {{ $dd && $dd->status == 'Accepted' ? 'selected' : '' }}>
                                            Accepted
                                        </option>
                                        <option value="Rejected" {{ $dd && $dd->status == 'Rejected' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="dd_date">DD Date:</label>
                                    <input type="date" name="dd_date" class="form-control" id="dd_date"
                                        value="{{ $dd->dd_date }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('dd_date') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="dd_no">DD No.:</label>
                                    <input type="text" name="dd_no" class="form-control" id="dd_no"
                                        value="{{ $dd->dd_no }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('dd_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="req_no">
                                        Courier request No.:
                                    </label>
                                    <select name="req_no" class="form-control" id="req_no">
                                        <option value="">-- Select --</option>
                                        @foreach ($couriers as $courier)
                                            <option value="{{ $courier->id }}"
                                                {{ $dd->req_no == $courier->id ? 'selected' : '' }}>
                                                {{ $courier->id }} - {{ $courier->to_org }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('req_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="remarks">Remarks (if any):</label>
                                    <input type="text" name="remarks" class="form-control" id="remarks"
                                        value="{{ $dd->remarks }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                                    </small>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $dd->action == 2 ? 'flex' : 'none' }}" id="ddfollowup">
                                <div class="col-md-4">
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
                                        <div class="row" id="popfollowups">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="fp[0][name]" class="form-control" id="name"
                                                    placeholder="Name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="number" name="fp[0][phone]" class="form-control"
                                                    id="phone" placeholder="Phone">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="email" name="fp[0][email]" class="form-control"
                                                    id="email" placeholder="Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="start_date">Followup Start From:</label>
                                        <input type="date" name="start_date" class="form-control" id="start_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="frequency">Followup Frequency:</label>
                                        <select name="frequency" id="frequency" class="form-control">
                                            <option value="">choose</option>
                                            @foreach ($ferq as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 stop" style="display: none">
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
                                <div class="col-md-4 stop_proof" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Please give proof:</label>
                                        <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                        <input type="file" name="proof_img" class="form-control mt-2" id="proof_img">
                                    </div>
                                </div>
                                <div class="col-md-4 stop_rem" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Write Remarks:</label>
                                        <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $dd->action == 3 ? 'flex' : 'none' }}"
                                id="ddcourier">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="docket_no">Docket No.:</label>
                                        <input type="date" name="docket_no" class="form-control" id="docket_no"
                                            value="{{ $dd->docket_no }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="docket_slip">Upload Docket Slip:</label>
                                        <input type="file" name="docket_slip" class="form-control" id="docket_slip">
                                        <a href="{{ asset('uploads/accounts/' . $dd->docket_slip) }}"
                                            class="text-primary pt-2" target="_blank">View</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $dd->action == 4 ? 'flex' : 'none' }}"
                                id="ddtransfer">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="transfer_date">Transfer Date:</label>
                                        <input type="date" name="transfer_date" class="form-control"
                                            id="transfer_date" value="{{ $dd->transfer_date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="utr">UTR Number:</label>
                                        <input type="text" name="utr" class="form-control" id="utr" value="{{ $dd->utr }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $dd->action == 7 ? 'flex' : 'none' }}"
                                id="ddcancel">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="date">Date:</label>
                                        <input type="date" name="date" class="form-control" id="date" value="{{ $dd->date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">Amount credited:</label>
                                        <input type="number" name="amount" class="form-control" id="amount" value="{{ $dd->amount }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="reference_no">Bank reference No:</label>
                                        <input type="text" name="reference_no" class="form-control"
                                            id="reference_no" value="{{ $dd->reference_no }}">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-primary">Save changes</button>
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
            let fp = 1;
            $(document).on('click', '.addPopFollowup', function(e) {
                let html = `
                <div class="col-md-4 form-group">
                    <input type="text" name="fp[${fp}][name]" class="form-control" id="name" placeholder="Name">
                </div>
                <div class="col-md-4 form-group">
                    <input type="number" name="fp[${fp}][phone]" class="form-control" id="phone" placeholder="Phone">
                </div>
                <div class="col-md-4 form-group">
                    <input type="email" name="fp[${fp}][email]" class="form-control" id="email" placeholder="Email">
                </div>
            `;
                $('#popfollowups').append(html);
                fp++;
            });
            // Utility function to handle option changes
            function handleOptionChange(actionSelector, config) {
                $(actionSelector).on('change', function() {
                    const value = $(this).val();
                    config.forEach(({
                        val,
                        selectorsToShow,
                        reqFld
                    }) => {
                        if (value == val) {
                            selectorsToShow.forEach(selector => {
                                $(selector).show();
                                reqFld.forEach(s => $(s).prop('required', true));
                            });
                        } else {
                            selectorsToShow.forEach(selector => {
                                $(selector).hide();
                                reqFld.forEach(s => $(s).prop('required', false));
                            });
                        }
                    });
                });
                $(actionSelector).prop('required', true);
            }

            // Configurations for each action dropdown
            handleOptionChange('#ddaction', [{
                    val: '1',
                    selectorsToShow: ['#ddaccount'],
                    reqFld: ['#status', '#dd_date', '#dd_no']
                },
                {
                    val: '2',
                    selectorsToShow: ['#ddfollowup'],
                    reqFld: ['#org_name', '#name', '#phone', '#frequency']
                },
                {
                    val: '3',
                    selectorsToShow: ['#ddcourier'],
                    reqFld: ['#docket_no', '#docket_slip']
                },
                {
                    val: '4',
                    selectorsToShow: ['#ddtransfer'],
                    reqFld: ['#transfer_date', '#utr']
                },
                {
                    val: '7',
                    selectorsToShow: ['#ddcancel'],
                    reqFld: ['#date', '#amount', '#reference_no']
                },
            ]);

            // Show/hide '.stop' based on frequency value
            $("select[name='frequency']").on('change', function() {
                if ($(this).val() == '6') {
                    $('.stop').show();
                } else {
                    $('.stop').hide();
                }
            });

            $("select[name='stop_reason']").on('change', function() {
                if ($(this).val() == '2') {
                    $('.stop_proof').show();
                } else {
                    $('.stop_proof').hide();
                }
                if ($(this).val() == '4') {
                    $('.stop_rem').show();
                } else {
                    $('.stop_rem').hide();
                }
            });
        });
    </script>
@endpush
