@extends('layouts.app')
@section('page-title', 'Tender Document Checklist')
@section('content')
    @php
        $docs = [
            'PAN & GST' => 'PAN & GST',
            'MSME' => 'MSME',
            'Mandate form' => 'Mandate form',
            'Cancelled Cheque' => 'Cancelled Cheque',
            'Incorporation/Registration' => 'Incorporation/Registration',
            'certificate' => 'certificate',
            'Board Resolution/POA' => 'Board Resolution/POA',
            'Electrical License' => 'Electrical License',
            'Net Worth Certificate - Latest' => 'Net Worth Certificate - Latest',
            'Solvency Certificate - Latest' => 'Solvency Certificate - Latest',
            'Financial Info - Latest' => 'Financial Info - Latest',
            'ISO 9001 & ISO 140001' => 'ISO 9001 & ISO 140001',
            'FIO Certificate' => 'FIO Certificate',
            'ESI & PF' => 'ESI & PF',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center m-2">
                        @if (Auth::user()->role == 'admin')
                            <div class="form-group" style="max-width: 200px">
                                <select id="team-filter" class="form-select">
                                    <option value="">All Teams</option>
                                    <option value="AC">AC</option>
                                    <option value="DC">DC</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @include('partials.messages')
                        <ul class="nav nav-pills justify-content-center" id="checklist" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending"
                                    type="button">
                                    Document Checklist Pending
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                                    Document Checklist Submitted
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            @foreach (['pending', 'submitted'] as $type)
                                <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}"
                                    id="{{ $type }}">
                                    <div class="table-responsive">
                                        <table class="table-hover" id="{{ $type }}Table">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender</th>
                                                    <th>Team Member</th>
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
            </div>
        </div>
    </section>

    <!-- Combined Document Checklist Modal -->
    <div class="modal fade" id="uploadResult1Modal" tabindex="-1" role="dialog" aria-labelledby="uploadResult1ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadResult1ModalLabel">Document Checklist</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleMode">View Only</button>
                </div>

                <!-- Edit Form -->
                <form id="uploadResultForm" action="{{ route('checklist.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tender_id" id="tender_id">
                    <div class="modal-body row" id="editSection">
                        <div class="form-group col-md-12">
                            <label for="docs" class="form-label">Select Document</label>
                            <div class="mb-2">
                                <input type="checkbox" id="selectAllDocs" class="form-check-input">
                                <label for="selectAllDocs" class="form-check-label"><strong>Select All</strong></label>
                            </div>
                            <div class="row">
                                @foreach ($docs as $key => $doc)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input doc-checkbox" type="checkbox"
                                                value="{{ $key }}" id="docs{{ $key }}" name="check[]">
                                            <label class="form-check-label" for="{{ $key }}">
                                                {{ $doc }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="table-responsive">
                                <table class="table-bordered w-100" id="checklistTable">
                                    <thead>
                                        <tr>
                                            <th class="h6">Name</th>
                                            <th class="text-end">
                                                <button type="button" class="btn btn-info btn-xs addDocs">Add</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="editFooter">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

                <!-- View Only Section -->
                <div class="modal-body" id="viewSection" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="viewChecklistTable">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" id="viewFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const tables = {};
        const tableTypes = ['pending', 'submitted'];
        let customIndex = 0;

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
                    url: `/document-checklist/data/${type}`,
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
                        data: 'tender_name',
                        name: 'tender_name'
                    },
                    {
                        data: 'users.name',
                        name: 'users.name'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
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
                    handleModalEvents();
                }
            });
        }

        $(document).ready(function() {
            const savedTeam = localStorage.getItem('selectedTeam');
            if (savedTeam) {
                $('#team-filter').val(savedTeam);
            }
            $('#team-filter').on('change', function() {
                const selectedTeam = $(this).val();
                localStorage.setItem('selectedTeam', selectedTeam);

                const activeTab = $('#checklist .nav-link.active').attr('data-bs-target').replace('#', '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#checklist button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const type = $(e.target).data('bs-target').replace('#', '');
                initializeTable(type);
            });

            setInterval(function() {
                tableTypes.forEach(type => {
                    if (tables[type]) {
                        tables[type].ajax.reload(null, false);
                    }
                });
            }, 300000);

            // Toggle between edit and view mode
            $('#toggleMode').on('click', function() {
                const isViewMode = $('#viewSection').is(':visible');
                if (isViewMode) {
                    // Switch to edit mode
                    $('#viewSection, #viewFooter').hide();
                    $('#editSection, #editFooter').show();
                    $(this).text('View Only');
                } else {
                    // Switch to view mode
                    $('#editSection, #editFooter').hide();
                    $('#viewSection, #viewFooter').show();
                    $(this).text('Edit Mode');
                }
            });

            // Add row to checklistTable in modal
            $(document).on('click', '.addDocs', function() {
                $('#checklistTable tbody').append(`
                    <tr>
                        <td>
                            <input type="text" name="doc_names[]" class="form-control" placeholder="Document Name" required>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-danger btn-xs removeDocRow">Remove</button>
                        </td>
                    </tr>
                `);
            });

            // Remove row from checklistTable in modal
            $(document).on('click', '.removeDocRow', function() {
                $(this).closest('tr').remove();
            });

            // Handle both view and edit buttons
            $(document).on('click', '.view-checklist-btn, .upload-result-btn1', function() {
                const docs = $(this).data('docs');
                const tenderId = $(this).data('tender-id');
                const isViewOnly = $(this).hasClass('view-checklist-btn');

                // Reset form and populate data
                $('#uploadResultForm')[0].reset();
                $('#checklistTable tbody').empty();
                $('#tender_id').val(tenderId);
                customIndex = 0;

                // Populate view table
                const $viewTbody = $('#viewChecklistTable tbody');
                $viewTbody.empty();
                if (Array.isArray(docs) && docs.length > 0) {
                    docs.forEach(doc => {
                        $viewTbody.append(
                            `<tr><td>${doc.document_name || doc.name || doc}</td></tr>`);
                    });
                } else {
                    $viewTbody.append('<tr><td>No documents added.</td></tr>');
                }

                // Populate edit form
                if (Array.isArray(docs) && docs.length > 0) {
                    docs.forEach(doc => {
                        const docName = doc.document_name || doc.name || doc;
                        // Check if it's a predefined document
                        const checkbox = $(`input[name="check[]"][value="${docName}"]`);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        } else {
                            // Add as custom doc
                            const newRow = `
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" name="doc_names[]" value="${docName}" required>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-danger btn-xs removeDocRow">Remove</button>
                                    </td>
                                </tr>`;
                            $('#checklistTable tbody').append(newRow);
                            customIndex++;
                        }
                    });
                }

                // Show appropriate mode
                if (isViewOnly) {
                    $('#editSection, #editFooter').hide();
                    $('#viewSection, #viewFooter').show();
                    $('#toggleMode').text('Edit Mode');
                } else {
                    $('#viewSection, #viewFooter').hide();
                    $('#editSection, #editFooter').show();
                    $('#toggleMode').text('View Only');
                }

                $('#uploadResult1Modal').modal('show');
            });
        });

        function handleTimers() {
            document.querySelectorAll('.timer').forEach(startCountdown);
        }

        // Handle upload result button click
        function handleModalEvents() {
            $(document).on('click', '.upload-result-btn1', function() {
                const tenderId = $(this).data('tender-id');
                const existingDocs = $(this).data('docs') || [];

                // Reset form
                $('#uploadResultForm')[0].reset();
                $('#checklistTable tbody').empty();
                $('#uploadResult1Modal #tender_id').val(tenderId);

                // Reset custom document index
                customDocIndex = 0;

                // Pre-populate existing documents if any
                if (Array.isArray(existingDocs) && existingDocs.length > 0) {
                    existingDocs.forEach(doc => {
                        const docName = doc.document_name || doc.name || doc;

                        // Check if it's a predefined document
                        if (predefinedDocs[docName]) {
                            // Check the corresponding checkbox
                            $(`.predefined-doc[value="${docName}"]`).prop('checked', true);
                        } else {
                            // Add as custom document
                            const newRow = `
                        <tr>
                            <td>
                                <input type="text" name="custom_docs[]" class="form-control"
                                       value="${docName}" required>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-danger btn-sm removeDocRow">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </td>
                        </tr>
                    `;
                            $('#checklistTable tbody').append(newRow);
                            customDocIndex++;
                        }
                    });
                }

                $('#uploadResult1Modal').modal('show');
            });
        }
    </script>
@endpush
@push('scripts')
    <script>
        $(document).on('change', '#selectAllDocs', function() {
            $('.doc-checkbox').prop('checked', this.checked);
        });

        $(document).on('change', '.doc-checkbox', function() {
            if (!this.checked) {
                $('#selectAllDocs').prop('checked', false);
            } else if ($('.doc-checkbox:checked').length === $('.doc-checkbox').length) {
                $('#selectAllDocs').prop('checked', true);
            }
        });
    </script>
@endpush
