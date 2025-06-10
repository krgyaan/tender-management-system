@extends('layouts.app')
@section('page-title', 'RA Management')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- <a href="" class="btn btn-primary btn-sm"></a> --}}
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">RA Applicable</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">RA Completed</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Member</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Tender Value</th>
                                                    <th>Item</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($raApplicable as $tdr)
                                                    <tr>
                                                        <td>{{ $tdr->tender_no }}</td>
                                                        <td>{{ $tdr->tender_name }}</td>
                                                        <td>{{ $tdr->users->name }}</td>
                                                        <td>{{ optional($tdr->ra)->bid_submission_date }}</td>
                                                        <td>{{ format_inr($tdr->gst_values) }}</td>
                                                        <td>{{ $tdr->itemName ? $tdr->itemName->name : '' }}
                                                        </td>
                                                        <td>{{ $tdr->statuses->name }}</td>
                                                        <td>
                                                            <a href="{{ route('ra.show', $tdr->id) }}"
                                                                class="btn btn-xs btn-info">View</a>
                                                            <a href="#" class="btn btn-xs btn-primary schedule-ra"
                                                                data-bs-toggle="modal" data-bs-target="#scheduleRAModal"
                                                                data-ra-id="{{ $tdr->id }}"
                                                                data-tender="{{ $tdr->id }}">Schedule RA</a>
                                                            <a href="#" class="btn btn-xs btn-secondary upload-ra"
                                                                data-bs-toggle="modal" data-bs-target="#uploadRAModal"
                                                                data-ra-id="{{ $tdr->id }}"
                                                                data-tender="{{ $tdr->id }}">Upload RA Result</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Member</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Tender Value</th>
                                                    <th>Item</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($raCompleted as $tdr)
                                                    <tr>
                                                        <td>{{ $tdr->tender_no }}</td>
                                                        <td>{{ $tdr->tender_name }}</td>
                                                        <td>{{ $tdr->users->name }}</td>
                                                        <td>{{ optional($tdr->ra)->bid_submission_date }}</td>
                                                        <td>{{ format_inr($tdr->gst_values) }}</td>
                                                        <td>{{ $tdr->itemName ? $tdr->itemName->name : '' }}
                                                        </td>
                                                        <td>{{ $tdr->statuses->name }}</td>
                                                        <td>
                                                            <a href="{{ route('ra.show', $tdr->id) }}"
                                                                class="btn btn-xs btn-info">View</a>
                                                            <a href="#" class="btn btn-xs btn-primary schedule-ra"
                                                                data-bs-toggle="modal" data-bs-target="#scheduleRAModal"
                                                                data-ra-id="{{ $tdr->id }}"
                                                                data-tender="{{ $tdr->id }}">Schedule RA</a>
                                                            <a href="#" class="btn btn-xs btn-secondary upload-ra"
                                                                data-bs-toggle="modal" data-bs-target="#uploadRAModal"
                                                                data-ra-id="{{ $tdr->id }}"
                                                                data-tender="{{ $tdr->id }}">Upload RA Result</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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

    <!-- Schedule RA Modal -->
    <div class="modal fade" id="scheduleRAModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule RA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="scheduleRAForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Technically Qualified</label>
                            <div class="form-check">
                                <input type="radio" name="technically_qualified" value="yes" class="form-check-input"
                                    id="qualified-yes">
                                <label class="form-check-label" for="qualified-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="technically_qualified" value="no" class="form-check-input"
                                    id="qualified-no">
                                <label class="form-check-label" for="qualified-no">No</label>
                            </div>
                        </div>

                        <div class="mb-3 disqualification-section" style="display: none;">
                            <label class="form-label">Reason for
                                Disqualification</label>
                            <textarea name="disqualification_reason" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="qualification-section" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">No. of Qualified
                                    Parties</label>
                                <input type="number" name="qualified_parties_count" class="form-control">
                                <div class="form-check">
                                    <input type="checkbox" name="parties_count_unknown" class="form-check-input">
                                    <label class="form-check-label">Not Known</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name of Qualified
                                    Parties</label>
                                <div id="qualified-parties-container">
                                    <div class="input-group mb-2">
                                        <input type="text" name="qualified_parties[]" class="form-control">
                                        <button type="button" class="btn btn-success add-party">+</button>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="parties_names_unknown" class="form-check-input">
                                    <label class="form-check-label">Not Known</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RA Start Time</label>
                                <input type="datetime-local" name="start_time" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">RA End Time</label>
                                <input type="datetime-local" name="end_time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload RA Result Modal -->
    <div class="modal fade" id="uploadRAModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload RA Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="uploadRAForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Result</label>
                            <select name="ra_result" class="form-select">
                                <option value="won">Won</option>
                                <option value="lost">Lost</option>
                                <option value="h1_elimination">H1 Elimination</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">VE L1 at start of RA</label>
                            <div class="form-check">
                                <input type="radio" name="ve_l1_start" value="yes" class="form-check-input">
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="ve_l1_start" value="no" class="form-check-input">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Start Price</label>
                            <input type="number" name="ra_start_price" class="form-control" step="0.01">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Close Price</label>
                            <input type="number" name="ra_close_price" class="form-control" step="0.01">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">RA Close Time</label>
                            <input type="datetime-local" name="ra_close_time" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Screenshot of Qualified
                                Parties</label>
                            <input type="file" name="qualified_parties_screenshot" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Screenshot of
                                Decrements</label>
                            <input type="file" name="decrements_screenshot" class="form-control">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Upload Final Result</label>
                            <input type="file" name="final_result" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update form action when modal is opened
            $('.schedule-ra').click(function() {
                const raId = $(this).data('ra-id');
                const tender = $(this).data('tender');

                // Add hidden field for tender
                $('#scheduleRAForm').attr('action', `{{ route('ra-management.schedule', '') }}/${raId}`);
                $('#scheduleRAForm').append(`<input type="hidden" name="tender_no" value="${tender}">`);

                // Reset form fields
                $('#scheduleRAForm')[0].reset();
                $('.qualification-section').hide();
                $('.disqualification-section').hide();
            });

            // Upload RA modal
            $('.upload-ra').click(function() {
                const raId = $(this).data('ra-id');
                const tender = $(this).data('tender');
                const form = $('#uploadRAForm');

                // Remove any existing hidden fields
                form.find('input[name="tender_no"]').remove();

                // Set form action and add hidden field
                form.attr('action', `/ra-management/upload-result/${raId}`);
                form.append(`<input type="hidden" name="tender_no" value="${tender}">`);

                // Reset form fields
                form[0].reset();
            });

            // Handle technically qualified radio buttons
            $('input[name="technically_qualified"]').change(function() {
                if ($(this).val() === 'yes') {
                    $('.qualification-section').show();
                    $('.disqualification-section').hide();
                } else {
                    $('.qualification-section').hide();
                    $('.disqualification-section').show();
                }
            });

            // Add more qualified parties
            $('.add-party').click(function() {
                const container = $('#qualified-parties-container');
                const newRow = `
                <div class="input-group mb-2">
                    <input type="text" name="qualified_parties[]" class="form-control">
                    <button type="button" class="btn btn-danger remove-party">-</button>
                </div>
            `;
                container.append(newRow);
            });

            // Remove qualified party
            $(document).on('click', '.remove-party', function() {
                $(this).closest('.input-group').remove();
            });

            // Handle "Not Known" checkboxes
            $('input[name="parties_count_unknown"]').change(function() {
                const input = $(this).closest('.mb-3').find('input[name="qualified_parties_count"]');
                input.prop('disabled', this.checked);
            });

            $('input[name="parties_names_unknown"]').change(function() {
                const container = $('#qualified-parties-container');
                container.toggle(!this.checked);
            });
        });
    </script>
@endpush
