@extends('layouts.app')
@section('page-title', 'Initiate Followup')
@section('content')
    @php
        $areas = [
            '1' => 'PG Personal',
            '2' => 'Accounts',
            '3' => 'AC Team',
            '4' => 'DC team',
        ];
        $ferq = [
            '1' => 'Daily',
            '2' => 'Alternate Days',
            '3' => '2 times a day',
            '4' => 'Weekly (every Mon)',
            '5' => 'Twice a Week (every Mon & Thu)',
            '6' => 'Stop',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('followups.index') }}" class="btn btn-outline-danger btn-sm">
                        Back
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="{{ route('followups.update', $fup->id) }}" method="POST" class="needs-validation"
                            novalidate enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <input type="hidden" value="{{ $fup->id }}" name="id">
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="area">Area</label>
                                    <select name="area" id="area" class="form-control" required readonly>
                                        <option value="">choose</option>
                                        @foreach ($areas as $key => $area)
                                            <option value="{{ $area }}" {{ $fup->area == $area ? 'selected' : '' }}>
                                                {{ $area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="party_name">Organisation Name</label>
                                    <input type="text" name="party_name" id="party_name" class="form-control" required
                                        value="{{ $fup->party_name }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="number" step="any" name="amount" id="amount" class="form-control"
                                        required value="{{ $fup->amount }}" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="assigned_to">Followup Assigned to</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control" required readonly>
                                        <option value="">choose</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $fup->assigned_to == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="followup_for">Followup For</label>
                                    <select name="followup_for" id="followup_for" class="form-control" required readonly>
                                        <option value="">choose</option>
                                        @foreach ($reasons as $reason)
                                            <option value="{{ $reason->name }}"
                                                {{ $fup->followup_for == $reason->name ? 'selected' : '' }}>
                                                {{ $reason->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="popfollowup">
                                @if (count($fup->followPerson) > 0)
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table-bordered w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="p-1 fw-bold">Name</th>
                                                        <th class="p-1 fw-bold">Phone</th>
                                                        <th class="p-1 fw-bold">Email</th>
                                                        <th class="p-1 fw-bold"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fup->followPerson as $key => $followup)
                                                        <tr>
                                                            <td class="p-1">{{ $followup->name }}</td>
                                                            <td class="p-1">{{ $followup->phone }}</td>
                                                            <td class="p-1">{{ $followup->email }}</td>
                                                            <td class="p-1">
                                                                <a href="javascript:void(0)" data-id="{{ $followup->id }}"
                                                                    class="btn btn-danger btn-xs removeDdFollowup">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Contact details:</label>
                                    <a href="javascript:void(0)" class="addDdFollowup">Add Person</a>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" id="ddfollowups">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="fp[name][0]" class="form-control" id="name"
                                                    placeholder="Name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="number" name="fp[phone][0]" class="form-control"
                                                    id="phone" placeholder="Phone">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="email" name="fp[email][0]" class="form-control"
                                                    id="email" placeholder="Email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="start_from">Followup Start From:</label>
                                        <input type="date" name="start_from" class="form-control" id="start_from"
                                            value="{{ $fup->start_from }}" required>
                                        <small>The date field must be a date after or equal to today.</small>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="frequency">Followup Frequency:</label>
                                        <select name="frequency" id="frequency" class="form-control" required>
                                            <option value="">choose</option>
                                            @foreach ($ferq as $fr => $frq)
                                                <option value="{{ $fr }}"
                                                    {{ $fup->frequency == $fr ? 'selected' : '' }}>
                                                    {{ $frq }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3 stop" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label" for="stop_reason">Why Stop:</label>
                                        <select name="stop_reason" class="form-control" id="stop_reason">
                                            <option value="">choose</option>
                                            <option value="1">The person is getting angry/or has requested to stop</option>
                                            <option value="2">Followup Objective achieved</option>
                                            <option value="3">External Followup Initiated</option>
                                            <option value="4">Remarks</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3 stop_proof" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Please give proof:</label>
                                        <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                        <input type="file" name="proof_img" class="form-control mt-2" id="proof_img">
                                    </div>
                                </div>
                                <div class="col-md-4 pt-3 stop_rem" style="display: none">
                                    <div class="form-group">
                                        <label class="form-label">Write Remarks:</label>
                                        <textarea name="stop_rem" class="form-control" id="stop_rem"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 pt-3">
                                    <label class="form-label" for="detailed">Detailed Request</label>
                                    <textarea name="detailed" id="detailed" class="form-control">{{ $fup->details }}</textarea>
                                    <small class="text-danger detailedErr" style="display: none">*Please provide detailed
                                        request</small>
                                </div>
                                <div class="form-group col-md-3 pt-3">
                                    <label class="form-label" for="attachments">Attachments</label>
                                    <input type="file" name="attachments[]" class="form-control" id="attachments"
                                        multiple>
                                    @if ($fup->attachments)
                                        @if (count(json_decode($fup->attachments)) > 0)
                                            <ul class="list-group mt-2">
                                                @foreach (json_decode($fup->attachments) as $attachment)
                                                    <li class="list-group-item">
                                                        <a href="{{ asset('uploads/accounts/' . $attachment) }}"
                                                            target="_blank">{{ $attachment }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="comment">Comment</label>
                                    <textarea row="3" class="form-control" name="comment" id="comment" readonly>{{ $fup->comment }}</textarea>
                                </div>
                                <div class="form-group col-md-12 pt-3 text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // show error if #detailed is empty
            $('form').on('submit', function(e) {
                if ($('#detailed').val() == '') {
                    e.preventDefault();
                    $('.detailedErr').show();
                } else {
                    $('.detailedErr').hide();
                }
            });

            let fp = 1;
            $(document).on('click', '.addDdFollowup', function(e) {
                let html = `
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" name="fp[name][${fp}]" class="form-control" id="name" placeholder="Name">
                    </div>
                    <div class="col-md-4 form-group">
                        <input type="number" name="fp[phone][${fp}]" class="form-control" id="phone" placeholder="Phone">
                    </div>
                    <div class="col-md-4 form-group">
                        <input type="email" name="fp[email][${fp}]" class="form-control" id="email" placeholder="Email">
                    </div>
                </div>
                `;
                $('#ddfollowups').append(html);
                fp++;
            });

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

            $(document).on('click', '.removeDdFollowup', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let row = $(this).closest('tr');

                let url = "{{ route('followups.person-delete', ':id') }}".replace(':id', id);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            row.remove();
                        }
                    },
                    error: function(xhr) {
                        alert('Error deleting data' + xhr.responseText);
                    }
                });
            });

            let editor = async () => ClassicEditor.create(document.querySelector('#detailed'), editorConfig);
            editor().then(newEditor => {
                editor = newEditor;
                editor.data.set('{!! str_replace("\'s", '&apos;s', $fup->details) !!}');
            }).catch(error => {
                console.error(error);
            });

            FilePond.registerPlugin(FilePondPluginFileValidateType);
            $('#attachments').filepond({
                allowMultiple: true,
                storeAsFile: true,
                maxFiles: '5',
                maxTotalFileSize: '25MB',
                credits: false,
                acceptedFileTypes: [
                    'image/*',
                    'text/plain',
                    'application/doc',
                    'application/pdf',
                    'presentation/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                ],

                fileValidateTypeLabelExpectedTypesMap: {
                    'application/doc': '.doc',
                    'application/pdf': '.pdf',
                    'presentation/*': '.ppt',
                    'application/msword': '.doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': '.docx',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': '.pptx',
                    'application/vnd.ms-excel': '.xls',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': '.xlsx',
                },
                fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}'
            })
        });
    </script>
@endpush
