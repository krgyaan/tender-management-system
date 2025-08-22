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
                <ul class="nav nav-pills justify-content-center" id="quoteTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Pending
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                            Submitted
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    @foreach (['pending', 'submitted'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Enquiry No</th>
                                            <th>Enquiry Name</th>
                                            <th>Approx Value (₹)</th>
                                            <th>Final Price (₹)</th>
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

        <!-- Offcanvas: Quote Submission -->
        <div class="offcanvas offcanvas-start" data-bs-backdrop="false" tabindex="-1" id="quoteSubmissionPanel"
            aria-labelledby="quoteSubmissionPanelLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="quoteSubmissionPanelLabel">Quote Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <form id="quoteSubmissionForm" enctype="multipart/form-data" method="POST"
                    action="{{ route('pvt-quotes.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="hidden" name="enquiry_id" id="submit_enquiry_id">
                        <label for="submissionDateTime" class="form-label">Quote Submission Date & Time</label>
                        <input type="datetime-local" class="form-control" id="submissionDateTime"
                            name="quote_submission_datetime" required>
                    </div>

                    <div class="mb-3">
                        <label for="submittedDocuments" class="form-label">Upload Submitted Quotations and Other
                            Documents</label>
                        <input class="form-control" type="file" id="submittedDocuments" name="submitted_documents[]"
                            multiple>
                        <small class="text-muted">Allowed: PDF, DOCX, JPG, PNG (Max 10MB each)</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label">Contact Details</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addQuoteContactBtn">
                                <i class="fas fa-plus"></i> Add Contact
                            </button>
                        </div>
                        <div id="quoteContactContainer">
                            <div class="row g-2 mb-2 contact-row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Name" name="contacts[0][name]"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Organisation"
                                        name="contacts[0][organisation]" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone" name="contacts[0][phone]"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email" name="contacts[0][email]"
                                        required>
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
                        <button type="submit" class="btn btn-primary btn-sm">Submit Quote</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Offcanvas: Quotation Dropped -->
        <div class="offcanvas offcanvas-start" data-bs-backdrop="false" tabindex="-1" id="quotationDroppedPanel"
            aria-labelledby="quotationDroppedPanelLabel">
            <div class="offcanvas-header bg-danger text-white">
                <h5 class="offcanvas-title" id="quotationDroppedPanelLabel">Quotation Dropped</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <form id="quotationDroppedForm" method="POST" action="{{ route('pvt-quotes.dropped') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="hidden" name="enquiry_id" id="drop_enquiry_id">
                        <label for="missReason" class="form-label">Reason for Missing the Enquiry</label>
                        <select class="form-select" name="missed_reason" id="missReason" required>
                            <option value="">-- Select Reason --</option>
                            <option value="Not allowed by OEM">Not allowed by OEM</option>
                            <option value="Internal Delay">Internal Delay</option>
                            <option value="Pricing Issue">Pricing Issue</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="oemSelectWrapper">
                        <label for="selectOEM" class="form-label">Select OEM</label>
                        <select class="form-select" id="selectOEM" name="oem_name">
                            <option value="">-- Select OEM --</option>
                            <option value="OEM A">OEM A</option>
                            <option value="OEM B">OEM B</option>
                            <option value="OEM C">OEM C</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="preventRepeat" class="form-label">What would you do to ensure this is not
                            repeated?</label>
                        <textarea class="form-control" id="preventRepeat" name="prevent_repeat" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tmsImprovement" class="form-label">Any improvements needed in the TMS system?</label>
                        <textarea class="form-control" id="tmsImprovement" name="tms_improvement" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm me-2"
                            data-bs-dismiss="offcanvas">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Submit Drop Reason</button>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script>
        // on click of submit-quote-btn and drop-quote-btn pass the enquiry id to the form
        document.addEventListener('click', function(e) {
            if (e.target.closest('.submit-quote-btn')) {
                const enquiryId = e.target.closest('.submit-quote-btn').getAttribute('data-enquiry-id');
                document.getElementById('submit_enquiry_id').value = enquiryId;
            }
            if (e.target.closest('.drop-quote-btn')) {
                const enquiryId = e.target.closest('.drop-quote-btn').getAttribute('data-enquiry-id');
                document.getElementById('drop_enquiry_id').value = enquiryId;
            }
        });


        // Handle OEM dropdown visibility
        document.getElementById('missReason').addEventListener('change', function() {
            const oemWrapper = document.getElementById('oemSelectWrapper');
            oemWrapper.classList.toggle('d-none', this.value !== 'Not allowed by OEM');
        });

        // Add/remove contact fields in Quote Submission panel
        let contactIndex = 1;
        document.getElementById('addQuoteContactBtn').addEventListener('click', function() {
            const container = document.getElementById('quoteContactContainer');
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 contact-row';
            newRow.innerHTML = `
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Name" name="contacts[${contactIndex}][name]" required>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Organisation" name="contacts[${contactIndex}][organisation]" required>
            </div>
            <div class="col-md-6">
                <input type="tel" class="form-control" placeholder="Phone" name="contacts[${contactIndex}][phone]" required>
            </div>
            <div class="col-md-6">
                <input type="email" class="form-control" placeholder="Email" name="contacts[${contactIndex}][email]" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
            container.appendChild(newRow);
            contactIndex++;
        });

        // Remove contact row
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-contact')) {
                e.target.closest('.contact-row').remove();
            }
        });


        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['pending', 'submitted'];

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
                    url: `/pvt-quote/data/${type}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            console.error(xhr.responseJSON.message);
                            console.log(data)
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
                        data: 'approx_value',
                        name: 'approx_value',
                    },
                    {
                        data: 'final_price',
                        name: 'final_price',
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
                },
            });
        }

        $(document).ready(function() {
            // Initialize the active tab based on saved state or default to 'pending'
            const activeTab = localStorage.getItem('activeQuoteTab') || 'pending';
            $(`#quoteTabs button[data-bs-target="#${activeTab}"]`).tab('show');
            initializeTable(activeTab);

            // Tab change handler
            $('#quoteTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const type = $(e.target).data('bs-target').replace('#', '');
                localStorage.setItem('activeQuoteTab', type);
                initializeTable(type);
            });

            // Auto-refresh
            setInterval(function() {
                const activeTab = localStorage.getItem('activeQuoteTab') || 'pending';
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
