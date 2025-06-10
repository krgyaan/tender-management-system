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
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Document Checklist Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Document Checklist
                                        Sumnitted</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table " id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Executive</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pendingTenders as $tender)
                                                    <tr>
                                                        <td>{{ $tender->tender_no }}</td>
                                                        <td>{{ $tender->tender_name }}</td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                            {{ $tender->due_date ? \Carbon\Carbon::parse($tender->due_date)->format('d-m-Y') : '' }}
                                                        </td>
                                                        <td>{{ $tender->users->name }}</td>
                                                        <td>{{ $tender->statuses->name }}</td>
                                                        <td>
                                                            @php
                                                                $timer = $tender->getTimer('document_checklist');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $tender->remainedTime(
                                                                        'document_checklist',
                                                                    );
                                                                }
                                                            @endphp
                                                            @if ($timer)
                                                                {{-- Sortable timer --}}
                                                                <span class="d-none">{{ $remaining }}</span>
                                                                <span class="timer" id="timer-{{ $tender->id }}"
                                                                    data-remaining="{{ $remaining }}"></span>
                                                            @else
                                                                <span class="d-none">0</span>
                                                                {!! $remained !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-info btn-xs upload-result-btn1"
                                                                data-tender-id="{{ $tender->id }}">Checklist</button>
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
                                        <table class="table " id="allUsers">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Team Executive</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($submittedTenders as $tender)
                                                    <tr>
                                                        <td>{{ $tender->tender_no }}</td>
                                                        <td>{{ $tender->tender_name }}</td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                            {{ $tender->due_date ? \Carbon\Carbon::parse($tender->due_date)->format('d-m-Y') : '' }}
                                                        </td>
                                                        <td>{{ $tender->users->name }}</td>
                                                        <td>{{ $tender->statuses->name }}</td>
                                                        <td>
                                                            @php
                                                                $timer = $tender->getTimer('document_checklist');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $tender->remainedTime(
                                                                        'document_checklist',
                                                                    );
                                                                }
                                                            @endphp
                                                            @if ($timer)
                                                                {{-- Sortable timer --}}
                                                                <span class="d-none">{{ $remaining }}</span>
                                                                <span class="timer" id="timer-{{ $tender->id }}"
                                                                    data-remaining="{{ $remaining }}"></span>
                                                            @else
                                                                <span class="d-none">0</span>
                                                                {!! $remained !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-info btn-xs upload-result-btn1"
                                                                data-tender-id="{{ $tender->id }}">Checklist</button>
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
                    <h5 class="modal-title" id="uploadResult1ModalLabel">Document Checklist</h5>
                </div>
                <form id="uploadResultForm" action="{{ route('checklist.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tender_id" id="tender_id">
                    <div class="modal-body row">
                        <div class="form-group col-md-12">
                            <label for="docs" class="form-label">Select Document</label>
                            <div class="row">
                                @foreach ($docs as $key => $doc)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $key }}"
                                                id="{{ $key }}" id="docs{{ $key }}" name="check[]">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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


            // Handle Add Document button click
            let n = 0;
            $('.addDocs').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="docs[${n}][name]" placeholder="Document Name" required>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-danger btn-xs removeDocs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#checklistTable tbody').append(newRow);
                n++;
            });

            // Handle Remove Document button click (using event delegation)
            $('#checklistTable').on('click', '.removeDocs', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endpush
