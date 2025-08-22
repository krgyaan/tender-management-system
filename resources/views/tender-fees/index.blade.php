@extends('layouts.app')
@section('page-title', 'All Tender Fee')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-none justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#tender-fees-modal">
                        Tender Fees (other than TMS)
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav class="navbar navbar-expand-lg">
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link btn btn-close-white active" id="dd-tab" data-bs-toggle="tab"
                                        data-bs-target="#dd-tab" type="button" role="tab" aria-controls="dd-tab"
                                        aria-selected="true">
                                        Demand Draft (DD)
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="bt-tab" data-bs-toggle="tab"
                                        data-bs-target="#bt-tab" type="button" role="tab" aria-controls="bt-tab"
                                        aria-selected="false">
                                        Bank Transfer
                                    </button>
                                    <button class="nav-link btn btn-close-white" id="pop-tab" data-bs-toggle="tab"
                                        data-bs-target="#pop-tab" type="button" role="tab" aria-controls="pop-tab"
                                        aria-selected="false">
                                        Pay on Portal
                                    </button>
                                </div>
                            </nav>
                            <div class="tab-content pt-3" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="dd-tab-content" role="tabpanel"
                                    aria-labelledby="dd-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>DD No.</th>
                                                    <th>Payee Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>Expiry</th>
                                                    <th>DD Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($ddTenderFees && count($ddTenderFees) > 0)
                                                    @foreach ($ddTenderFees as $dd)
                                                        <tr>
                                                            <td>{{ $dd->created_at->format('d-m-Y') }}</td>
                                                            <td>{{ $dd->dd_no }}</td>
                                                            <td>{{ $dd->in_favour_of }}</td>
                                                            <td>{{ $dd->tender?->tender_name }}</td>
                                                            <td>{{ format_inr($dd->dd_amount) }}</td>
                                                            <td>{{ $dd->expiry_date }}</td>
                                                            <td>{{ $dd->status }}</td>
                                                            <td>
                                                                @php
                                                                    $timer = strtotime($dd->created_at);
                                                                    $remainingTime = $timer - 24 * 60 * 60;
                                                                    $currentTime = time();
                                                                    $timeRemaining = $remainingTime - $currentTime;
                                                                @endphp
                                                                <span class="timer" id="timer-{{ $dd->id }}"
                                                                    data-remaining="{{ $timeRemaining }}">
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button type="button" data-id="{{ $dd->id }}"
                                                                    data-bs-target="#demandDraftModal"
                                                                    data-bs-toggle="modal" data-type="demandDraft"
                                                                    class="btn btn-xs btn-info feeStatusBtn">
                                                                    Status
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bt-tab-content" role="tabpanel" aria-labelledby="bt-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>UTR No</th>
                                                    <th>Account name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>Tender Status</th>
                                                    <th>Tender Fees<br> Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($btTenderFees && count($btTenderFees) > 0)
                                                    @foreach ($btTenderFees as $bt)
                                                        <tr>
                                                            <td>{{ $bt->created_at->format('d-m-Y') }}</td>
                                                            <td>{{ $bt->utr }}</td>
                                                            <td>{{ $bt->account_name }}</td>
                                                            <td>{{ $bt->tender?->tender_name }}</td>
                                                            <td>{{ format_inr($bt->amount) }}</td>
                                                            <td>{{ $bt->tender?->statuses->name }}</td>
                                                            <td>{{ $bt->status }}</td>
                                                            <td>
                                                                @php
                                                                    $timer = strtotime($bt->created_at);
                                                                    $remainingTime = $timer - 24 * 60 * 60;
                                                                    $currentTime = time();
                                                                    $timeRemaining = $remainingTime - $currentTime;
                                                                @endphp
                                                                <span class="timer" id="timer-{{ $bt->id }}"
                                                                    data-remaining="{{ $timeRemaining }}">
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button type="button" data-id="{{ $bt->id }}"
                                                                    data-bs-target="#bankTransferModal"
                                                                    data-bs-toggle="modal" data-type="bankTransfer"
                                                                    class="btn btn-xs btn-info feeStatusBtn">
                                                                    Status
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pop-tab-content" role="tabpanel" aria-labelledby="pop-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>UTR No.</th>
                                                    <th>Portal Name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>Tender Status</th>
                                                    <th>Pay on Portal <br> EMD Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($popTenderFees && count($popTenderFees) > 0)
                                                    @foreach ($popTenderFees as $pop)
                                                        <tr>
                                                            <td>{{ $pop->created_at->format('d-m-Y') }}</td>
                                                            <td>{{ $pop->utr }}</td>
                                                            <td>{{ $pop->portal_name }}</td>
                                                            <td>{{ $pop->tender?->tender_name }}</td>
                                                            <td>{{ format_inr($pop->amount) }}</td>
                                                            <td>{{ $pop->tender?->statuses->name }}</td>
                                                            <td>{{ $pop->tender?->emd_status }}</td>
                                                            <td>
                                                                @php
                                                                    $timer = strtotime($pop->created_at);
                                                                    $remainingTime = $timer - 24 * 60 * 60;
                                                                    $currentTime = time();
                                                                    $timeRemaining = $remainingTime - $currentTime;
                                                                @endphp
                                                                <span class="timer" id="timer-{{ $pop->id }}"
                                                                    data-remaining="{{ $timeRemaining }}">
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button type="button" data-id="{{ $pop->id }}"
                                                                    data-bs-target="#payOnPortalModal"
                                                                    data-bs-toggle="modal" data-type="payOnPortal"
                                                                    class="btn btn-xs btn-info feeStatusBtn">
                                                                    Status
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Modal Template --}}
        @foreach (['bankTransfer', 'payOnPortal', 'demandDraft'] as $type)
            <div class="modal fade" id="{{ $type }}Modal" tabindex="-1" role="dialog"
                aria-labelledby="{{ $type }}ModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="{{ $type }}ModalLabel">
                                {{ ucfirst($type) == 'DemandDraft' ? 'Demand Draft' : (ucfirst($type) == 'PayOnPortal' ? 'Pay on Portal' : 'Bank Transfer') }}
                                Tender Fees
                            </h5>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('tender-fees.status') }}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="type" value="{{ $type }}">
                                <div class="form-group">
                                    <label for="status">Request Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="reason">Reason for Rejection</label>
                                    <textarea name="reason" id="reason" class="form-control"></textarea>
                                </div>
                                @if ($type == 'demandDraft')
                                    <div class="form-group">
                                        <label for="dd_no">DD Number</label>
                                        <input type="text" name="dd_no" id="dd_no" class="form-control">
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="utr">UTR Number</label>
                                        <input type="text" name="utr" id="utr" class="form-control">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="utr_msg">UTR Message</label>
                                    <textarea name="utr_msg" id="utr_msg" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea name="remark" id="remark" class="form-control"></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.feeStatusBtn').on('click', function() {
                var id = $(this).data('id');
                var type = $(this).data('type');
                var modal = $('#' + type + 'Modal');
                modal.find('input[name="id"]').val(id);
                modal.modal('show');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
        $(document).ready(function() {
            let buttons = document.querySelectorAll('button.nav-link');
            let tabContents = document.querySelectorAll('.tab-pane');

            function activateTab(button) {
                buttons.forEach(function(btn) {
                    btn.classList.remove('active');
                });

                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                    content.classList.remove('show');
                });

                button.classList.add('active');
                let tabId = button.getAttribute('id');

                let activeContent = document.querySelector(`#${tabId}-content`);
                if (activeContent) {
                    activeContent.classList.add('active');
                    activeContent.classList.add('show');
                }

                localStorage.setItem('activeTab', tabId);
            }

            buttons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    activateTab(button);
                });
            });

            window.addEventListener('load', function() {
                let activeTabId = localStorage.getItem('activeTab');
                if (activeTabId) {
                    let activeButton = document.getElementById(activeTabId);
                    if (activeButton) {
                        activateTab(activeButton);
                    }
                } else {
                    let firstButton = buttons[0];
                    activateTab(firstButton);
                }
            });
        });
    </script>
@endpush
