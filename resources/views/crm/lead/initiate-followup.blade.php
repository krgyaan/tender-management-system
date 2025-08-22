@extends('layouts.app')
@section('page-title', 'Initiate Followup on Lead')
@section('content')
    @php
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
        <div class="col-md-12">
            <a href="{{ route('lead.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <nav class="d-flex justify-content-between">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="btn btn-success nav-link active" id="nav-mail-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-mail" type="button" role="tab" aria-controls="nav-mail"
                            aria-selected="true">Follow-up
                            Mail</button>
                        <button class="btn btn-success nav-link" id="nav-call-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-call" type="button" role="tab" aria-controls="nav-call"
                            aria-selected="false">Cold Call
                            Done</button>
                        <button class="btn btn-success nav-link" id="nav-visit-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-visit" type="button" role="tab" aria-controls="nav-visit"
                            aria-selected="false">Cold
                            Visit</button>
                        <button class="btn btn-success nav-link" id="nav-letter-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-letter" type="button" role="tab" aria-controls="nav-letter"
                            aria-selected="false">Letter
                            Sent</button>
                        <button class="btn btn-success nav-link" id="nav-whatsapp-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-whatsapp" type="button" role="tab" aria-controls="nav-whatsapp"
                            aria-selected="false">Whatsapp
                            Sent</button>
                    </div>
                </nav>
                <div class="tab-content mt-5" id="nav-tabContent">
                    <!-- Follow-up Mail Tab -->
                    <div class="tab-pane fade show active" id="nav-mail" role="tabpanel" aria-labelledby="nav-mail-tab"
                        tabindex="0">
                        <form method="POST" enctype="multipart/form-data" class="row" id="form-followup-mail"
                            action="{{ route('leads.followup.mail', $lead->id) }}">
                            @csrf
                            <div class="mb-3 col-md-12">
                                <label for="mailText" class="form-label">Mail Body</label>
                                <textarea class="form-control" id="mailText" name="mailText" rows="3">{{ old('mailText') }}</textarea>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="mailAttachment" class="form-label">Add Attachment (if any)</label>
                                <input class="form-control" type="file" id="mailAttachment" name="mailAttachment"
                                    value="{{ old('mailAttachment') }}">
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="frequency" class="form-label">Frequency</label>
                                <select class="form-select" id="frequency" name="frequency">
                                    @foreach ($ferq as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('frequency') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 pt-3 stop" style="display: none">
                                <div class="form-group">
                                    <label class="form-label" for="stop_reason">Why Stop:</label>
                                    <select name="stop_reason" class="form-control" id="stop_reason">
                                        <option value="">choose</option>
                                        <option value="1" {{ old('stop_reason') == '1' ? 'selected' : '' }}>
                                            The person is getting angry/or has requested to stop
                                        </option>
                                        <option value="2" {{ old('stop_reason') == '2' ? 'selected' : '' }}>Followup
                                            Objective achieved</option>
                                        <option value="3" {{ old('stop_reason') == '3' ? 'selected' : '' }}>External
                                            Followup Initiated</option>
                                        <option value="4" {{ old('stop_reason') == '4' ? 'selected' : '' }}>Remarks
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 pt-3 stop_proof" style="display: none">
                                <div class="form-group">
                                    <label class="form-label">Please give proof:</label>
                                    <textarea name="proof_text" class="form-control mb-2" id="proof_text"></textarea>
                                    <input type="file" name="proof_img" class="form-control mt-2" id="proof_img"
                                        value="{{ old('proof_img') }}">
                                </div>
                            </div>
                            <div class="col-md-4 pt-3 stop_rem" style="display: none">
                                <div class="form-group">
                                    <label class="form-label">Write Remarks:</label>
                                    <textarea name="stop_rem" class="form-control" id="stop_rem">{{ old('stop_rem') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- Cold Call Done Tab -->
                    <div class="tab-pane fade" id="nav-call" role="tabpanel" aria-labelledby="nav-call-tab"
                        tabindex="0">
                        <form method="POST" class="row" id="form-cold-call"
                            action="{{ route('leads.followup.call', $lead->id) }}">
                            @csrf
                            <div class="mb-3 col-md-6">
                                <label for="callPoints" class="form-label">Points Discussed</label>
                                <textarea class="form-control" id="callPoints" name="callPoints" rows="5">{{ old('callPoints') }}</textarea>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="callResponsibility" class="form-label">VE Responsibility</label>
                                <textarea class="form-control" id="callResponsibility" name="callResponsibility" rows="5">{{ old('callResponsibility') }}</textarea>
                            </div>
                            <div id="mail-contact-details-wrapper">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Contact details:</label>
                                    <a href="javascript:void(0)" class="add-mail-contacts">Add Person</a>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" id="mail-contacts">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <input type="text" name="mail[name][0]" class="form-control"
                                                    id="name" placeholder="Name" value="{{ old('mail.name.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="text" name="mail[designation][0]" class="form-control"
                                                    id="designation" placeholder="Designation"
                                                    value="{{ old('mail.designation.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="number" name="mail[phone][0]" class="form-control"
                                                    id="phone" placeholder="Phone"
                                                    value="{{ old('mail.phone.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="email" name="mail[email][0]" class="form-control"
                                                    id="email" placeholder="Email"
                                                    value="{{ old('mail.email.0') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="callNextDate" class="form-label">Next Follow-up Date</label>
                                <input type="date" class="form-control" id="callNextDate" name="callNextDate"
                                    value="{{ old('callNextDate') }}">
                            </div>
                            <div class="form-group col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- Cold Visit Tab -->
                    <div class="tab-pane fade" id="nav-visit" role="tabpanel" aria-labelledby="nav-visit-tab"
                        tabindex="0">
                        <form method="POST" class="row" id="form-cold-visit"
                            action="{{ route('leads.followup.visit', $lead->id) }}">
                            @csrf
                            <div class="mb-3 col-md-6">
                                <label for="visitPoints" class="form-label">Points Discussed</label>
                                <textarea class="form-control" id="visitPoints" name="visitPoints" rows="5">{{ old('visitPoints') }}</textarea>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="visitResponsibility" class="form-label">VE Responsibility</label>
                                <textarea class="form-control" id="visitResponsibility" name="visitResponsibility" rows="5">{{ old('visitResponsibility') }}</textarea>
                            </div>
                            <div id="cold-contact-details-wrapper">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Contact details:</label>
                                    <a href="javascript:void(0)" class="add-cold-contacts">Add Person</a>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" id="cold-contacts">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <input type="text" name="cold[name][0]" class="form-control"
                                                    id="name" placeholder="Name" value="{{ old('cold.name.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="text" name="cold[designation][0]" class="form-control"
                                                    id="designation" placeholder="Designation"
                                                    value="{{ old('cold.designation.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="number" name="cold[phone][0]" class="form-control"
                                                    id="phone" placeholder="Phone"
                                                    value="{{ old('cold.phone.0') }}">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <input type="email" name="cold[email][0]" class="form-control"
                                                    id="email" placeholder="Email"
                                                    value="{{ old('cold.email.0') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="visitNextDate" class="form-label">Next Follow-up Date</label>
                                <input type="date" class="form-control" id="visitNextDate" name="visitNextDate"
                                    value="{{ old('visitNextDate') }}">
                            </div>
                            <div class="form-group col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- Letter Sent Tab -->
                    <div class="tab-pane fade" id="nav-letter" role="tabpanel" aria-labelledby="nav-letter-tab"
                        tabindex="0">
                        <form method="POST" class="row" id="form-letter-sent"
                            action="{{ route('leads.followup.letter', $lead->id) }}">
                            @csrf
                            <div class="form-group col-md-4 mb-3">
                                <label for="letterCourierNo" class="form-label">Courier Request No.</label>
                                <input type="text" class="form-control" id="letterCourierNo" name="letterCourierNo"
                                    value="{{ old('letterCourierNo') }}">
                            </div>
                            <div class="form-group col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- Whatsapp Sent Tab -->
                    <div class="tab-pane fade" id="nav-whatsapp" role="tabpanel" aria-labelledby="nav-whatsapp-tab"
                        tabindex="0">
                        <form method="POST" class="row" id="form-whatsapp-sent"
                            action="{{ route('leads.followup.whatsapp', $lead->id) }}">
                            @csrf
                            <div class="form-group col-md-6 mb-3">
                                <label for="whatsappText" class="form-label">What you have sent?</label>
                                <textarea class="form-control" id="whatsappText" name="whatsappText" rows="3">{{ old('whatsappText') }}</textarea>
                            </div>
                            <div class="form-group col-md-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
            // Restore active tab from localStorage if available
            const activeTab = localStorage.getItem('leadFollowupActiveTab');
            if (activeTab) {
                const tabTrigger = new bootstrap.Tab(document.getElementById(activeTab + '-tab'));
                tabTrigger.show();
            }

            // Store active tab in localStorage when a tab is clicked
            $('button[data-bs-toggle="tab"]').on('click', function(e) {
                const tabId = $(this).attr('id').replace('-tab', '');
                localStorage.setItem('leadFollowupActiveTab', tabId);
            });

            let editor = async () => ClassicEditor.create(document.querySelector('#mailText'), editorConfig);
            editor().then(newEditor => {
                editor = newEditor;
            }).catch(error => {
                console.error(error);
            });

            let cold = 1;
            $(document).on('click', '.add-cold-contacts', function(e) {
                let html = `
                <div class="row">
                    <div class="col-md-3 form-group">
                        <input type="text" name="cold[name][${cold}]" class="form-control" id="name" placeholder="Name">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="text" name="cold[designation][${cold}]" class="form-control" id="designation" placeholder="Designation">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="number" name="cold[phone][${cold}]" class="form-control" id="phone" placeholder="Phone">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="email" name="cold[email][${cold}]" class="form-control" id="email" placeholder="Email">
                    </div>
                </div>
                `;
                $('#cold-contacts').append(html);
                cold++;
            });

            let mail = 1;
            $(document).on('click', '.add-mail-contacts', function(e) {
                let html = `
                <div class="row">
                    <div class="col-md-3 form-group">
                        <input type="text" name="mail[name][${mail}]" class="form-control" id="name" placeholder="Name">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="text" name="mail[designation][${mail}]" class="form-control" id="designation" placeholder="Designation">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="number" name="mail[phone][${mail}]" class="form-control" id="phone" placeholder="Phone">
                    </div>
                    <div class="col-md-3 form-group">
                        <input type="email" name="mail[email][${mail}]" class="form-control" id="email" placeholder="Email">
                    </div>
                </div>
                `;
                $('#mail-contacts').append(html);
                mail++;
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
        });
    </script>
@endpush
