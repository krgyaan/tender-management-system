@extends('layouts.app')
@section('page-title', 'Cheque Actions')
@php
    $ferq = [
        '1' => 'Daily',
        '2' => 'Alternate Days',
        '3' => '2 times a day',
        '4' => 'Weekly (every Mon)',
        '5' => 'Twice a Week (every Mon & Thu)',
        '6' => 'Stop',
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
                                <div class="col-md-4 mb-3">
                                    <label for="action" class="form-label">Choose What to do:</label>
                                    <select class="form-control" id="chqaction" name="action">
                                        <option value="">Select Action</option>
                                        <option {{ $cheque->action == '1' ? 'selected' : '' }} value="1">
                                            Accounts Form
                                        </option>
                                        <option {{ $cheque->action == '2' ? 'selected' : '' }} value="2">
                                            Initiate Followup
                                        </option>
                                        <option {{ $cheque->action == '3' ? 'selected' : '' }} value="3">
                                            Stop the cheque from the bank
                                        </option>
                                        <option {{ $cheque->action == '4' ? 'selected' : '' }} value="4">
                                            Paid via Bank Transfer
                                        </option>
                                        <option {{ $cheque->action == '5' ? 'selected' : '' }} value="5">
                                            Deposited in Bank
                                        </option>
                                        <option {{ $cheque->action == '6' ? 'selected' : '' }} value="6">
                                            Cancelled/Torn
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $cheque->action == '1' ? 'flex' : 'none' }}"
                                id="chqaccount">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="status">
                                        Cheque request:
                                    </label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">-- Select --</option>
                                        <option {{ $cheque->status == 'Accepted' ? 'selected' : '' }} value="Accepted">
                                            Accepted</option>
                                        <option {{ $cheque->status == 'Rejected' ? 'selected' : '' }} value="Rejected">
                                            Rejected</option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="reason">Reason for Rejection:</label>
                                    <input type="text" name="reason" class="form-control" id="reason"
                                        value="{{ $cheque->reason }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('reason') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="cheq_no">Cheque No.:</label>
                                    <input type="text" name="cheq_no" class="form-control" id="cheq_no"
                                        value="{{ $cheque->cheq_no }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('cheq_no') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="duedate">Due date (if payable):</label>
                                    <input type="date" name="duedate" class="form-control" id="duedate"
                                        value="{{ $cheque->duedate }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('duedate') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="handover">
                                        Receiving of the cheque handed over:
                                    </label>
                                    <input type="file" name="handover" class="form-control" id="handover">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('handover') }}</span>
                                    </small>
                                    @if ($cheque->handover)
                                        <a href="{{ asset('uploads/accounts/' . $cheque->handover) }}"
                                            class="text-primary me-3" target="_blank">View</a>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="cheq_img">Upload soft copy of Cheque (both sides):</label>
                                    <input type="file" name="cheq_img[]" class="form-control" id="cheq_img" multiple>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('cheq_img') }}</span>
                                    </small>

                                    @if ($cheque->cheq_img)
                                        <div class="">
                                            @foreach (explode(',', $cheque->cheq_img) as $img)
                                                <a href="{{ asset('uploads/accounts/' . $img) }}" class="text-primary me-3"
                                                    target="_blank">View {{ $loop->iteration }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="confirmation">
                                        Upload Positive pay confirmation copy:
                                    </label>
                                    <input type="file" name="confirmation" class="form-control" id="confirmation">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('confirmation') }}</span>
                                    </small>

                                    @if ($cheque->confirmation)
                                        <a href="{{ asset('uploads/accounts/' . $cheque->confirmation) }}"
                                            class="text-primary me-3" target="_blank">View</a>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="remarks">Remarks (if any):</label>
                                    <input type="text" name="remarks" class="form-control" id="remarks"
                                        value="{{ $cheque->remarks }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                                    </small>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $cheque->action == '2' ? 'flex' : 'none' }}"
                                id="chqfollowup">
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
                                                <input type="text" name="fp[0][name]" class="form-control"
                                                    id="name" placeholder="Name">
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

                            <div class="row" style="display: {{ $cheque->action == '3' ? 'flex' : 'none' }}"
                                id="chqcancel">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="stop_reason_text">Reason for Stopping of
                                            Cheque:</label>
                                        <textarea name="stop_reason_text" class="form-control" id="stop_reason_text" rows="3">{{ $cheque->stop_reason_text }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $cheque->action == '4' ? 'flex' : 'none' }}"
                                id="chqtransfer">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="transfer_date">Transfer Date:</label>
                                        <input type="date" name="transfer_date" class="form-control"
                                            id="transfer_date" value="{{ $cheque->transfer_date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">UTR Amount:</label>
                                        <input type="number" name="amount" class="form-control" id="amount"
                                            value="{{ $cheque->amount }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="utr">UTR Number:</label>
                                        <input type="text" name="utr" class="form-control" id="utr"
                                            value="{{ $cheque->utr }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $cheque->action == '5' ? 'flex' : 'none' }}"
                                id="chqbank">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="bt_transfer_date">Transfer Date:</label>
                                        <input type="date" name="bt_transfer_date" class="form-control"
                                            id="bt_transfer_date" value="{{ $cheque->bt_transfer_date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="reference">Bank Reference No:</label>
                                        <input type="text" name="reference" class="form-control" id="reference"
                                            value="{{ $cheque->reference }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: {{ $cheque->action == '6' ? 'flex' : 'none' }}"
                                id="chqtorn">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="cancelled_img"> Upload Photo/confirmation from
                                            Beneficiary:</label>
                                        <input type="file" name="cancelled_img" class="form-control"
                                            id="cancelled_img">
                                        @if ($cheque->cancelled_img)
                                            <a href="{{ asset('uploads/accounts/' . $cheque->cancelled_img) }}"
                                                target="_blank">View</a>
                                        @endif
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
            handleOptionChange('#chqaction', [{
                    val: '1',
                    selectorsToShow: ['#chqaccount'],
                    reqFld: ['#status', '#cheq_img', '#handover', '#cheq_no']
                },
                {
                    val: '2',
                    selectorsToShow: ['#chqfollowup'],
                    reqFld: ['#org_name', '#name', '#phone', '#frequency']
                },
                {
                    val: '3',
                    selectorsToShow: ['#chqcancel'],
                    reqFld: ['#stop_reason_text']
                },
                {
                    val: '4',
                    selectorsToShow: ['#chqtransfer'],
                    reqFld: ['#transfer_date', '#utr', '#amount']
                },
                {
                    val: '5',
                    selectorsToShow: ['#chqbank'],
                    reqFld: ['#bt_transfer_date', '#reference']
                },
                {
                    val: '6',
                    selectorsToShow: ['#chqtorn'],
                    reqFld: ['#cancelled_img']
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

            $('#cheq_img').filepond({
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: '2',
                credits: false,
            });
        });
    </script>
@endpush
