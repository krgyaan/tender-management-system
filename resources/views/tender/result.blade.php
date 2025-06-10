@extends('layouts.app')
@section('page-title', 'Tender Result')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-awaited-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-awaited" type="button" role="tab"
                                        aria-controls="nav-awaited" aria-selected="true">Result Awaited</button>
                                    <button class="nav-link" id="nav-won-tab" data-bs-toggle="tab" data-bs-target="#nav-won"
                                        type="button" role="tab" aria-controls="nav-won"
                                        aria-selected="true">WON</button>
                                    <button class="nav-link" id="nav-lost-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-lost" type="button" role="tab" aria-controls="nav-lost"
                                        aria-selected="false">LOST</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-awaited" role="tabpanel"
                                    aria-labelledby="nav-awaited-tab">
                                </div>
                                <div class="tab-pane fade show active" id="nav-won" role="tabpanel"
                                    aria-labelledby="nav-won-tab">
                                    <div class="table-responsive">
                                        <table class="table " id="won">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Executive</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Tender Value</th>
                                                    <th>Item</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tenders as $tender)
                                                    <tr>
                                                        <td>{{ $tender->tender_no }}</td>
                                                        <td>{{ $tender->tender_name }}</td>
                                                        <td>{{ $tender->users->name }}</td>
                                                        <td>
                                                            {{ $tender->bs ? date('d-m-Y', strtotime(optional($tender->bs)->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td>{{ format_inr($tender->tender_value) }}</td>
                                                        <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                        <td>{{ $tender->statuses->name }}</td>
                                                        <td>
                                                            <a href="" class="btn btn-info btn-xs">View</a>
                                                            <button type="button"
                                                                class="btn btn-secondary btn-xs upload-result-btn1"
                                                                data-tender-id="{{ $tender->id }}">Parties</button>
                                                            <button type="button"
                                                                class="btn btn-primary btn-xs upload-result-btn2"
                                                                data-tender-id="{{ $tender->id }}">Result</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-lost" role="tabpanel" aria-labelledby="nav-lost-tab">
                                    <div class="table-responsive">
                                        <table class="table " id="lost">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Executive</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Tender Value</th>
                                                    <th>Item</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tenders as $tender)
                                                    <tr>
                                                        <td>{{ $tender->tender_no }}</td>
                                                        <td>{{ $tender->tender_name }}</td>
                                                        <td>{{ $tender->users->name }}</td>
                                                        <td>
                                                            {{ $tender->bs ? date('d-m-Y', strtotime(optional($tender->bs)->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td>{{ format_inr($tender->tender_value) }}</td>
                                                        <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                        <td>{{ $tender->statuses->name }}</td>
                                                        <td>
                                                            <a href="" class="btn btn-info btn-xs">View</a>
                                                            <button type="button"
                                                                class="btn btn-secondary btn-xs upload-result-btn1"
                                                                data-tender-id="{{ $tender->id }}">Parties</button>
                                                            <button type="button"
                                                                class="btn btn-primary btn-xs upload-result-btn2"
                                                                data-tender-id="{{ $tender->id }}">Result</button>
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
                    <input type="hidden" name="tender_id" id="tender_id">
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
                    <input type="hidden" name="tender_id" id="tender_id">
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
        $(document).ready(function() {
            // Handle Upload Result1 button click
            $('.upload-result-btn1').click(function() {
                var tenderId = $(this).data('tender-id');
                $('#tender_id').val(tenderId);
                $('#uploadResult1Modal').modal('show');
            });
            // Handle Upload Result2 button click
            $('.upload-result-btn2').click(function() {
                var tenderId = $(this).data('tender-id');
                $('#tender_id').val(tenderId);
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
