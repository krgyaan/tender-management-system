@extends('layouts.app')
@section('page-title', 'Tender Result')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
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
                @include('partials.messages')

                <ul class="nav nav-pills justify-content-center" id="resultsTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                            Result Awaited
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#won" type="button">
                            Won
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lost" type="button">
                            Lost
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    @foreach (['pending', 'won', 'lost'] as $type)
                        <div class="tab-pane fade {{ $type === 'pending' ? 'show active' : '' }}" id="{{ $type }}">
                            <div class="table-responsive">
                                <table class="table-hover" id="{{ $type }}Table">
                                    <thead>
                                        <tr>
                                            <th>Tender</th>
                                            <th>Member</th>
                                            <th>Item</th>
                                            <th>Status</th>
                                            <th>Emd Details</th>
                                            <th>Tender Value</th>
                                            <th>Bid Submission Date</th>
                                            <th>Result Status</th>
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
    </section>

    <!-- Upload Result2 Modal -->
    <div class="modal fade" id="uploadResult1Modal" tabindex="-1" role="dialog" aria-labelledby="uploadResult1ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadResult1ModalLabel">Upload Tender Result</h5>
                </div>
                <form id="uploadResultForm" action="{{ route('results.storeTechnical') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tender_id" id="tender_id_1">
                    <div class="modal-body row">
                        <div class="form-group col-md-6">
                            <label for="technically_qualified">Technically Qualified</label>
                            <select class="form-control" id="technically_qualified" name="technically_qualified" required>
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div id="disqualification_reason_div" style="display: none;">
                            <div class="form-group">
                                <label for="disqualification_reason">Reason for Disqualification</label>
                                <textarea class="form-control" id="disqualification_reason" name="disqualification_reason" rows="3"></textarea>
                            </div>
                        </div>
                        <div id="qualified_parties_div" style="display: none;" class="row">
                            <div class="form-group col-md-6">
                                <label for="qualified_parties_count">No. of Qualified Parties</label>
                                <input type="text" class="form-control" id="qualified_parties_count"
                                    name="qualified_parties_count" placeholder="Enter number or 'not known'">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="qualified_parties_names">Name of Qualified Parties</label>
                                <div id="qualified_parties_names_container">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="qualified_parties_names[]"
                                            placeholder="Enter party name or 'not known'">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary add-party-btn"
                                                type="button">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="qualified_parties_screenshot">Upload Screenshot of Qualified Parties</label>
                                <input type="file" class="form-control" id="qualified_parties_screenshot"
                                    name="qualified_parties_screenshot[]">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Result2 Modal -->
    <div class="modal fade" id="uploadResult2Modal" tabindex="-1" role="dialog"
        aria-labelledby="uploadResult2ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadResult2ModalLabel">Upload Tender Result</h5>
                </div>
                <form id="uploadResultForm" action="{{ route('results.storeFinal') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tender_id" id="tender_id_2">
                    <div class="modal-body row">

                        <div class="form-group col-md-6">
                            <label for="result">Result</label>
                            <select class="form-control" id="result" name="result">
                                <option value="">Select</option>
                                <option value="won">Won</option>
                                <option value="lost">Lost</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="l1_price">L1 Price</label>
                            <input type="number" class="form-control" id="l1_price" name="l1_price" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="l2_price">L2 Price</label>
                            <input type="number" class="form-control" id="l2_price" name="l2_price" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="our_price">Our Price</label>
                            <input type="number" class="form-control" id="our_price" name="our_price" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="final_result">Upload Final Result</label>
                            <input type="file" class="form-control" id="final_result" name="final_result">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let fp = 1;
        $(document).on('click', '.addPopFollowup', function(e) {
            console.log('Add Followup Person button clicked');

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
            $('#emd-followups').append(html);
            fp++;
        });
        $(document).on('click', '.update-emd-status-btn', function() {
            console.log('Update EMD Status button clicked');
            var target = $(this).attr('data-bs-target');

            var offcanvasElem = document.querySelector(target);
            if (offcanvasElem) {
                var offcanvas = new bootstrap.Offcanvas(offcanvasElem);
                console.log(offcanvas);
                offcanvas.show();
            } else {
                console.error('Offcanvas element not found:', target);
            }

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
        // window.alert = function(msg) { console.log("Intercepted alert:", msg); }

        const tables = {};
        const tableTypes = ['pending', 'won', 'lost'];

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
                    url: `/result/data/${type}`,
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
                        data: 'item_name.name',
                        name: 'item_name.name'
                    },
                    {
                        data: 'tender_status',
                        name: 'tender_status'
                    },
                    {
                        data: 'emd_details',
                        name: 'emd_details'
                    },
                    {
                        data: 'final_price',
                        name: 'final_price'
                    },
                    {
                        data: 'bid_submissions_date',
                        name: 'bid_submissions_date',
                    },
                    {
                        data: 'result_status',
                        name: 'result_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [6, 'desc']
                ],
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
            const savedTeam = localStorage.getItem('selectedTeam');
            if (savedTeam) {
                $('#team-filter').val(savedTeam);
            }
            $('#team-filter').on('change', function() {
                const selectedTeam = $(this).val();
                localStorage.setItem('selectedTeam', selectedTeam);

                // Refresh only the active tab
                const activeTab = $('#resultsTabs .nav-link.active').attr('data-bs-target').replace('#',
                    '');

                if (tables[activeTab]) {
                    tables[activeTab].ajax.reload();
                }
            });

            initializeTable('pending');

            $('#resultsTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
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
        });

        function handleTimers() {
            document.querySelectorAll('.timer').forEach(startCountdown);
        }
    </script>
@endpush

@push('scripts')
    <script>
        $(document).on('shown.bs.offcanvas', function(e) {
            // Find the offcanvas that was just shown
            var $offcanvas = $(e.target);
            var $emdStatus = $offcanvas.find('#emd_status');
            var $followup = $offcanvas.find('.followup');

            function toggleFollowup() {
                if ($emdStatus.val() == '1') {
                    $followup.show();
                } else {
                    $followup.hide();
                }
            }

            $emdStatus.off('change.toggleFollowup').on('change.toggleFollowup', toggleFollowup);
            toggleFollowup();
        });
    </script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle Upload Result1 button click (delegated)
            $(document).on('click', '.upload-result-btn1', function() {
                var tenderId = $(this).data('tender-id');
                $('#tender_id_1').val(tenderId);
                $('#uploadResult1Modal').modal('show');
            });
            // Handle Upload Result2 button click (delegated)
            $(document).on('click', '.upload-result-btn2', function() {
                var tenderId = $(this).data('tender-id');
                $('#tender_id_2').val(tenderId);
                $('#uploadResult2Modal').modal('show');
            });

            // Handle technically qualified change
            $('#technically_qualified').change(function() {
                if ($(this).val() === 'no') {
                    $('#disqualification_reason_div').show();
                    $('#qualified_parties_div').hide();
                } else if ($(this).val() === 'yes') {
                    $('#disqualification_reason_div').hide();
                    $('#qualified_parties_div').show();
                } else {
                    $('#disqualification_reason_div').hide();
                    $('#qualified_parties_div').hide();
                }
            });

            // Add new party name field
            $(document).on('click', '.add-party-btn', function() {
                var newPartyField = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="qualified_parties_names[]" placeholder="Enter party name or 'not known'">
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger remove-party-btn" type="button">-</button>
                        </div>
                    </div>
                `;
                $('#qualified_parties_names_container').append(newPartyField);
            });

            // Remove party name field
            $(document).on('click', '.remove-party-btn', function() {
                $(this).closest('.input-group').remove();
            });

            $('#qualified_parties_screenshot').filepond({
                allowMultiple: true,
                credits: false,
                storeAsFile: true,
                maxTotalFileSize: '25MB',
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
            $('#final_result').filepond({
                allowMultiple: false,
                credits: false,
                storeAsFile: true,
                maxTotalFileSize: '25MB',
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            });
        });
    </script>
@endpush
