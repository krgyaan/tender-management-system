@extends('layouts.app')
@section('page-title', 'Enquiries Management')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('enquiries.create') }}" class="btn btn-sm btn-primary">Add New Enquiry</a>
                    </div>
                </div>
                @include('partials.messages')
                <ul class="nav nav-pills justify-content-center" id="enquiryTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ac" type="button">
                            Team AC
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dc" type="button">
                            Team DC
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ib" type="button">
                            Team IB
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    @foreach (['ac', 'dc', 'ib'] as $type)
                        <div class="tab-pane fade {{ $type === 'ac' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Enquiry No</th>
                                            <th>Enquiry Name</th>
                                            <th>BD Lead</th>
                                            <th>Company</th>
                                            <th>Organization</th>
                                            <th>Item</th>
                                            <th>Approx Value (₹)</th>
                                            <th>Site Visit</th>
                                            <th>Status</th>
                                            <th>Timer</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Offcanvas 1: Allocate Site Visit -->
        <div class="offcanvas offcanvas-start" data-bs-backdrop="false" tabindex="-1" id="allocateSiteVisitPanel"
            aria-labelledby="allocateSiteVisitPanelLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="allocateSiteVisitPanelLabel">Allocate Site Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="allocateSiteVisitForm" method="POST" action="{{ route('enquiries.allocate-site-visit') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="enquiry_id">
                        <label for="siteVisitDate" class="form-label">Site Visit Planned Date</label>
                        <input type="datetime-local" name="visit_date_time" class="form-control" id="siteVisitDate"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="visitAssignee" class="form-label">Site Visit to be done by</label>
                        <select class="form-select" id="visitAssignee" name="assignee_id" required>
                            <option value="">Select Person</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="visitNotes" class="form-label">Additional Instructions</label>
                        <textarea class="form-control" id="visitNotes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="offcanvas">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Allocate Visit</button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Offcanvas 2: Record Site Visit Details -->
        <div class="offcanvas offcanvas-start" data-bs-backdrop="false" tabindex="-1" id="siteVisitDetailsPanel"
            aria-labelledby="siteVisitDetailsPanelLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="siteVisitDetailsPanelLabel">Site Visit Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="siteVisitDetailsForm" enctype="multipart/form-data" method="POST"
                    action="{{ route('enquiries.record-site-visit') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="site_visit_id">
                        <label for="visitInformation" class="form-label">Site Visit Information Received</label>
                        <textarea class="form-control" id="visitInformation" name="information" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="visitDocuments" class="form-label">Upload Documents/Photos/Videos</label>
                        <input class="form-control" type="file" id="visitDocuments" name="visit_documents[]" multiple
                            accept=".pdf,.jpg,.jpeg,.png,.mp4">
                        <small class="text-muted">Max file size: 10MB each</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label">Contact Details</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addContactBtn">
                                <i class="fas fa-plus"></i> Add Contact
                            </button>
                        </div>
                        <div id="contactDetailsContainer">
                            <div class="row g-2 mb-2 contact-row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Name"
                                        name="contacts[0][name]">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Designation"
                                        name="contacts[0][designation]">
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone"
                                        name="contacts[0][phone]">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email"
                                        name="contacts[0][email]">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm me-2"
                            data-bs-dismiss="offcanvas">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Submit Details</button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Offcanvas 3: Submit Costing Sheet -->
        <div class="offcanvas offcanvas-start" data-bs-backdrop="false" tabindex="-1" id="submitCostingSheetPanel"
            aria-labelledby="submitCostingSheetPanelLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="submitCostingSheetPanelLabel">Submit Costing Sheet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="submitCostingSheetForm" method="POST"
                    action="{{ route('private-costing-sheet.submitSheet') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <input type="hidden" name="costing_id">
                            <label for="finalPrice" class="form-label">Final Price (GST Inclusive)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="finalPrice" name="final_price" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="receiptPreGst" class="form-label">Receipt (Pre GST)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="receiptPreGst" name="receipt_pre_gst" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="budgetPreGst" class="form-label">Budget (Pre GST)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="budgetPreGst" name="budget_pre_gst" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="grossMargin" class="form-label">Gross Margin %</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="grossMargin" name="gross_margin" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="costingRemarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="costingRemarks" name="costing_remarks" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm me-2"
                            data-bs-dismiss="offcanvas">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Submit Costing</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $(document).on('click', '.allocate-visit-btn', function(e) {
                var enquiryId = $(this).data('enquiry-id');
                console.log(enquiryId);

                $('#allocateSiteVisitForm').find('input[name="enquiry_id"]').val(enquiryId);
            })

            $(document).on('click', '.record-visit-btn', function(e) {
                var visitId = $(this).data('visit-id');
                console.log(visitId);

                $('#siteVisitDetailsForm').find('input[name="site_visit_id"]').val(visitId);
            })

            $(document).on('click', '.submit-costing-btn', function(e) {
                var costingId = $(this).data('costing-id');

                // Calculate Gross Margin =((Receipt-Budget)/(Receipt)) in %age
                $(document).on('input', '#receiptPreGst, #budgetPreGst', function() {
                    let receipt = parseFloat($('#receiptPreGst').val()) || 0;
                    let budget = parseFloat($('#budgetPreGst').val()) || 0;
                    let gross_margin = receipt > 0 ? ((receipt - budget) / receipt) * 100 : 0;
                    $('#grossMargin').val(gross_margin.toFixed(2));
                });

                $('#submitCostingSheetForm').find('input[name="costing_id"]').val(costingId);
            })

            // Add contact fields dynamically
            let contactCounter = 1;
            $('#addContactBtn').click(function() {
                contactCounter++;
                $('#contactDetailsContainer').append(`
                    <div class="row g-2 mb-2 contact-row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Name" name="contacts[${contactCounter}][name]">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Designation" name="contacts[${contactCounter}][designation]">
                        </div>
                        <div class="col-md-6">
                            <input type="tel" class="form-control" placeholder="Phone" name="contacts[${contactCounter}][phone]">
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" placeholder="Email" name="contacts[${contactCounter}][email]">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `);
            });

            // Remove contact fields
            $(document).on('click', '.remove-contact', function() {
                if ($('.contact-row').length > 1) {
                    $(this).closest('.contact-row').remove();
                } else {
                    alert('At least one contact is required');
                }
            });
        });

        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['ac', 'dc', 'ib'];

        function initializeTable(type) {
            if (tables[type]) return;

            tables[type] = $(`#${type}Table`).DataTable({
                serverSide: true,
                orderCellsTop: true,
                processing: true,
                pageLength: 50,
                stateSave: true,
                stateLoadParams: function(settings, data) {
                    data.length = 50;
                },
                ajax: {
                    url: `/enquiries/data/${type}`,
                    method: 'GET',
                    data: function(d) {
                        d.team = $('#team-filter').val();
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
                        data: 'enquiry_no',
                        name: 'enquiry_no',
                    },
                    {
                        data: 'enquiry_name',
                        name: 'enquiry_name',
                    },
                    {
                        data: 'bd_lead',
                        name: 'bd_lead',
                    },
                    {
                        data: 'company_name',
                        name: 'company_name',
                        defaultContent: 'N/A',
                    },
                    {
                        data: 'organization_name',
                        name: 'organization_name',
                    },
                    {
                        data: 'item_name',
                        name: 'item_name',
                    },
                    {
                        data: 'approx_value',
                        name: 'approx_value',
                    },
                    {
                        data: 'site_visit',
                        name: 'site_visit',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'timer',
                        name: 'timer',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
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
                }
            });
        }

        $(document).ready(function() {
            // Initialize the active tab based on saved state or default to 'ac'
            const activeTab = localStorage.getItem('activeEnquiryTab') || 'ac';
            $(`#enquiryTabs button[data-bs-target="#${activeTab}"]`).tab('show');
            initializeTable(activeTab);

            // Tab change handler
            $('#enquiryTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const type = $(e.target).data('bs-target').replace('#', '');
                localStorage.setItem('activeEnquiryTab', type);
                initializeTable(type);
            });

            // Auto-refresh
            setInterval(function() {
                const activeTab = localStorage.getItem('activeEnquiryTab') || 'ac';
                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload(null, false);
                }
            }, 300000);
        });

        function handleTimers() {
            document.querySelectorAll('.timer').forEach(startCountdown);
        }
    </script>
@endpush
