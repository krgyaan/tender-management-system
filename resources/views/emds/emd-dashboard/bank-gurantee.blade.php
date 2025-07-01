@extends('layouts.app')
@section('page-title', 'Bank Guarantee Dashboard')
@section('content')
    @php
        use Carbon\Carbon;
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
        $bgStatus = [
            1 => 'Accounts Form 1 - Request to Bank',
            2 => 'Accounts Form 2 - After BG Creation',
            3 => 'Accounts Form 3 - Capture FDR Details',
            4 => 'Initiate Followup',
            5 => 'Request Extension',
            6 => 'Returned via courier',
            7 => 'Request Cancellation',
            8 => 'BG Cancellation Confirmation',
            9 => 'FDR Cancellation Confirmation',
        ];
        $banks = [
            'SBI' => 'State Bank of India',
            'HDFC_0026' => 'HDFC Bank',
            'ICICI' => 'ICICI Bank',
            'YESBANK_2011' => 'Yes Bank 2011',
            'YESBANK_0771' => 'Yes Bank 0771',
            'PNB_6011' => 'Punjab National Bank',
        ];
        $color = [
            'SBI' => 'bg-bank-sbi',
            'HDFC_0026' => 'bg-bank-hdfc',
            'ICICI' => 'bg-bank-icici',
            'YESBANK_2011' => 'bg-bank-yes2011',
            'YESBANK_0771' => 'bg-bank-yes0771',
            'PNB_6011' => 'bg-bank-pnb',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                @if ($groupedBg)
                    <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center mb-3">
                        @foreach ($bankStats as $bankName => $stats)
                            <div class="p-3 rounded shadow border position-relative {{ $color[$bankName] }}">
                                <h5 class="">
                                    {{ $banks[$bankName] }}
                                </h5>
                                <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-info">
                                    BG Created: {{ $stats['count'] }}
                                </span>
                                <p class="my-0 text-success">BG: ₹ {{ format_inr($stats['amount']) }}</p>
                                <p class="my-0 text-success">FDR (10%): ₹ {{ format_inr($stats['fdrAmount10']) }}</p>
                                <p class="my-0 text-success">FDR (15%): ₹ {{ format_inr($stats['fdrAmount15']) }}</p>
                                <p class="my-0 text-success">FDR (100%): ₹ {{ format_inr($stats['fdrAmount100']) }}</p>
                                <span
                                    class="position-absolute top-100 start-50 translate-middle badge rounded-pill bg-dark border border-light">
                                    {{ number_format($stats['percentage'], 2) }}% of BG
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    <a href="{{ route('bg-old-entry') }}" class="btn btn-info btn-sm">
                        Update Old Entries
                    </a>
                    <a href="{{ route('download-bgs') }}" class="btn btn-primary btn-sm">
                        Download All Bgs
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <ul class="nav nav-pills justify-content-center" id="bgTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#new_requests"
                                    type="button">
                                    New Requests
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#live" type="button">
                                    Live
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button">
                                    Cancelled
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                                    Rejected
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            @foreach (['new_requests', 'live', 'cancelled', 'rejected'] as $type)
                                <div class="tab-pane fade {{ $type === 'new_requests' ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead>
                                                <tr>
                                                    <th>BG Date</th>
                                                    <th>BG No.</th>
                                                    <th>Beneficiary name</th>
                                                    <th>Tender Name</th>
                                                    <th>Amount</th>
                                                    <th>BG Expiry Date</th>
                                                    <th>BG Claim Period<br> Expiry Date</th>
                                                    <th>BG Charges paid</th>
                                                    <th>BG Charges <br>Calculated</th>
                                                    <th>FDR No</th>
                                                    <th>FDR Value</th>
                                                    <th>Tender Status</th>
                                                    <th>Expiry</th>
                                                    <th>BG Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let bgTables = {};
            let types = ['new_requests', 'live', 'cancelled', 'rejected'];

            function initDataTable(type) {
                if (bgTables[type]) {
                    bgTables[type].ajax.reload();
                    return;
                }
                bgTables[type] = $(`#${type}Table`).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('bg.getBgData', ':type') }}'.replace(':type', type),
                        type: 'POST',
                        data: function(d) {
                            d
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables error:', error, thrown);
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                console.error(xhr.responseJSON.message);
                            } else {
                                console.error('Error loading data. Please try again.');
                            }
                        }
                    },
                    createdRow: function(row, data, dataIndex) {
                        let bankClass = '';
                        switch (data.bg_bank) {
                            case 'SBI':
                                bankClass = 'bg-bank-sbi';
                                break;
                            case 'HDFC_0026':
                                bankClass = 'bg-bank-hdfc';
                                break;
                            case 'ICICI':
                                bankClass = 'bg-bank-icici';
                                break;
                            case 'YESBANK_2011':
                                bankClass = 'bg-bank-yes2011';
                                break;
                            case 'YESBANK_0771':
                                bankClass = 'bg-bank-yes0771';
                                break;
                            case 'PNB_6011':
                                bankClass = 'bg-bank-pnb';
                                break;
                        }
                        if (bankClass) {
                            $(row).addClass(bankClass);
                        }
                    },
                    columns: [{
                            data: 'bg_date',
                            name: 'bg_date'
                        },
                        {
                            data: 'bg_no',
                            name: 'bg_no'
                        },
                        {
                            data: 'beneficiary_name',
                            name: 'beneficiary_name'
                        },
                        {
                            data: 'tender_name',
                            name: 'tender_name'
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                        },
                        {
                            data: 'bg_expiry',
                            name: 'bg_expiry'
                        },
                        {
                            data: 'bg_claim_expiry',
                            name: 'bg_claim_expiry'
                        },
                        {
                            data: 'bg_charges_paid',
                            name: 'bg_charges_paid'
                        },
                        {
                            data: 'bg_charges_calculated',
                            name: 'bg_charges_calculated',
                        },
                        {
                            data: 'fdr_no',
                            name: 'fdr_no'
                        },
                        {
                            data: 'fdr_value',
                            name: 'fdr_value',
                        },
                        {
                            data: 'tender_status',
                            name: 'tender_status'
                        },
                        {
                            data: 'expiry',
                            name: 'expiry'
                        },
                        {
                            data: 'bg_status',
                            name: 'bg_status'
                        },
                        {
                            data: 'timer',
                            name: 'timer',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }

                    ],
                    order: [
                        []
                    ],
                    search: {
                        return: true,
                    },
                    pageLength: 25,
                    language: {
                        zeroRecords: 'No matching records found',
                        emptyTable: 'No data available in table',
                        paginate: {
                            first: 'First',
                            previous: 'Previous',
                            next: 'Next',
                            last: 'Last'
                        }
                    },
                    responsive: true,
                    stateSave: true,
                    drawCallback: function() {
                        handleTimers();
                    },
                });
            }
            initDataTable('new_requests');

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let type = $(e.target).data('bs-target').replace('#', '');
                initDataTable(type);
            });

            function handleTimers() {
                document.querySelectorAll('.timer').forEach(startCountdown);
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .bg-bank-sbi {
            background-color: #e3f2fd !important;
        }

        .bg-bank-hdfc {
            background-color: #fff3e0 !important;
        }

        .bg-bank-icici {
            background-color: #fce4ec !important;
        }

        .bg-bank-yes2011 {
            background-color: #e8f5e9 !important;
        }

        .bg-bank-yes0771 {
            background-color: #f9fbe7 !important;
        }

        .bg-bank-pnb {
            background-color: #f3e5f5 !important;
        }
    </style>
@endpush
