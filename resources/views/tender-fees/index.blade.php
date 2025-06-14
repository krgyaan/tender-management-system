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
                                                                <a href="" class="btn btn-xs btn-info">
                                                                    Edit
                                                                </a>
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
                                                            <td>{{ $bt->utr_no }}</td>
                                                            <td>{{ $bt->account_name }}</td>
                                                            <td>{{ $bt->tender?->tender_name }}</td>
                                                            <td>{{ format_inr($bt->amount) }}</td>
                                                            <td>{{ $bt->tender?->statuses->first()->name }}</td>
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
                                                                <a href="" class="btn btn-xs btn-info">
                                                                    Edit
                                                                </a>
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
                                                            <td>{{ $pop->utr_no }}</td>
                                                            <td>{{ $pop->portal_name }}</td>
                                                            <td>{{ $pop->tender?->tender_name }}</td>
                                                            <td>{{ format_inr($pop->amount) }}</td>
                                                            <td>{{ $pop->tender?->statuses->first()->name }}</td>
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
                                                                <a href="" class="btn btn-xs btn-info">
                                                                    Edit
                                                                </a>
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
    </section>
@endsection

@push('scripts')
    <script>
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
