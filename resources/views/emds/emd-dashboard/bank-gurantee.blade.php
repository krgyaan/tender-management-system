@extends('layouts.app')
@section('page-title', 'Bank Guarantee Dashboard')
@section('content')
    @php
        use Carbon\Carbon;

    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                @if ($groupedBg)
                    <div class="d-flex flex-wrap gap-2 justify-content-center align-items-center mb-3">
                        @foreach ($bankStats as $bankName => $stats)
                            <div class="p-3 rounded shadow border position-relative">
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
                    <div class="btn-group" role="group" aria-label="Download BGs">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Download BGs
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('emds.export.bg', ['type' => 'all']) }}">All BANKS</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('emds.export.bg', ['type' => 'pnb']) }}">PN Bank</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('emds.export.bg', ['type' => 'yes']) }}">YES BANK</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('emds.export.bg', ['type' => 'limit']) }}">BG Limit</a>
                            </li>
                        </ul>
                    </div>
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
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#live_yes" type="button">
                                    Live YES
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#live_pnb" type="button">
                                    Live PNB
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#live_limit" type="button">
                                    Live BG Limit
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
                            @foreach (['new_requests', 'live_yes', 'live_pnb', 'live_limit', 'cancelled', 'rejected'] as $type)
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
            let types = ['new_requests', 'live_yes', 'live_pnb', 'live_limit', 'cancelled', 'rejected'];

            function initDataTable(type) {
                if (bgTables[type]) {
                    bgTables[type].ajax.reload();
                    return;
                }
                bgTables[type] = $(`#${type}Table`).DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 50,
                    stateSave: true,
                    ordering: true,
                    ajax: {
                        url: '{{ route('bg.getBgData', ':type') }}'.replace(':type', type),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
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
                    order: [],
                    search: {
                        return: true,
                    },
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
