@extends('layouts.app')
@section('page-title', 'Payment On Portal Actions')
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
    $popStatus = [
        1 => 'Accounts Form',
        2 => 'Initiate Followup',
        3 => 'Returned via Bank Transfer',
        4 => 'Settled with Project Account',
    ];
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="{{ route('pop-action', $pop->id) }}" method="post" enctype="multipart/form-data"
                            id="updateStatusForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="action" class="form-label">Choose What to do:</label>
                                    <select class="form-control" id="popaction" name="action" required>
                                        <option value="">Select Action</option>
                                        <option {{ $pop->action == '1' ? 'selected' : '' }} value="1">
                                            Accounts Form
                                        </option>
                                        <option {{ $pop->action == '2' ? 'selected' : '' }} value="2">
                                            Initiate Followup
                                        </option>
                                        <option {{ $pop->action == '3' ? 'selected' : '' }} value="3">
                                            Returned via Bank
                                            Transfer</option>
                                        <option {{ $pop->action == '4' ? 'selected' : '' }} value="4">
                                            Settled with Project Account
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $pop->action == '1' ? 'flex' : 'none' }}"
                                id="popaccount">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="status">
                                        Pay on Portal request:
                                    </label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">-- Select --</option>
                                        <option value="Accepted" {{ $pop && $pop->status == 'Accepted' ? 'selected' : '' }}>
                                            Accepted
                                        </option>
                                        <option value="Rejected" {{ $pop && $pop->status == 'Rejected' ? 'selected' : '' }}>
                                            Rejected
                                        </option>
                                    </select>
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="reason">Reason for Rejection:</label>
                                    <input type="text" name="reason" class="form-control" id="reason"
                                        value="{{ $pop->reason }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('reason') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="date_time">Date and Time of Payment:</label>
                                    <input type="datetime-local" name="date_time" class="form-control" id="date_time"
                                        value="">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('date_time') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="utr">UTR for the transaction:</label>
                                    <input type="text" name="utr" class="form-control" id="utr"
                                        value="{{ $pop->utr }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('utr') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="remarks">Remarks (if any):</label>
                                    <input type="text" name="remarks" class="form-control" id="remarks"
                                        value="{{ $pop->remarks }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                                    </small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="utr_mgs">UTR Message:</label>
                                    <input type="text" name="utr_mgs" class="form-control" id="utr_mgs"
                                        value="{{ $pop->utr_mgs }}">
                                    <small class="text-muted">
                                        <span class="text-danger">{{ $errors->first('utr_mgs') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="row" style="display: {{ $pop->action == '2' ? 'flex' : 'none' }}"
                                id="popfollowup">
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
                            <div class="row" style="display: {{ $pop->action == '3' ? 'flex' : 'none' }}"
                                id="poptransfer">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="transfer_date">Transfer Date:</label>
                                        <input type="date" name="transfer_date" class="form-control"
                                            id="transfer_date" value="{{ $pop->transfer_date }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="utr_num">UTR Number:</label>
                                        <input type="text" name="utr_num" class="form-control" id="utr_num"
                                            value="{{ $pop->utr_num }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="tenderPopFeeModal" tabindex="-1" aria-labelledby="tenderPopFeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderPopFeeModalLabel">Pop Tender Fees</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('tender-fees.pop.store') }}">
                        @csrf
                        <div class="row" id="bank_transfer">
                            <div class="col-md-12 form-group">
                                <input type="hidden" name="tender_id" value="">
                                <input type="hidden" name="emd_id" value="">
                                <label class="form-label" for="purpose">Purpose</label>
                                <input type="text" name="purpose" id="purpose" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('purpose') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="portal_name">Name of Portal</label>
                                <input type="text" name="portal_name" id="portal_name" class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('portal_name') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="netbanking">Netbanking available</label>
                                <select name="netbanking" id="netbanking" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('netbanking') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="bank_debit_card">Yes Bank Debit card</label>
                                <select name="bank_debit_card" id="bank_debit_card" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('bank_debit_card') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" step="any" name="amount" id="amount"
                                    class="form-control">
                                <small class="text-muted">
                                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                                </small>
                            </div>
                            <div class="col-md-12 form-group text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
            handleOptionChange('#popaction', [{
                    val: '1',
                    selectorsToShow: ['#popaccount'],
                    reqFld: ['#status', '#utr', '#utr_mgs']
                },
                {
                    val: '2',
                    selectorsToShow: ['#popfollowup'],
                    reqFld: ['#org_name', '#name', '#phone', '#frequency']
                },
                {
                    val: '3',
                    selectorsToShow: ['#poptransfer'],
                    reqFld: ['#status', '#transfer_date', '#utr_num']
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
                if ($(this).val() == '6') {
                    $('.stop_rem').show();
                } else {
                    $('.stop_rem').hide();
                }
            });

            let editor = async () => ClassicEditor.create(document.querySelector('#detailed'), editorConfig);
            editor().then(newEditor => {
                editor = newEditor;
            }).catch(error => {
                console.error(error);
            });
        });
    </script>
@endpush
