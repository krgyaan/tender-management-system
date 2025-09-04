@extends('layouts.app')
@section('page-title', 'Conference Call Details')

@section('content')
    <section class="py-4">
        <div class="card border-0 shadow-lg rounded-3 overflow-hidden">

            {{-- Card Body --}}
            <div class="card-body p-5">
                {{-- Conference Call Details --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Issue Description</label>
                        <div class="form-control bg-light border-0 shadow-sm">{{ $conferenceCall->issue_description }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Materials / Tools Required</label>
                        <div class="form-control bg-light border-0 shadow-sm">
                            {{ $conferenceCall->materials_required ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="text-muted small fw-semibold">Actions Planned</label>
                        <div class="form-control bg-light border-0 shadow-sm">
                            {{ $conferenceCall->actions_planned ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Voice Recording</label>
                        <div class="p-3 bg-light border rounded-3 shadow-sm text-center">
                            @if ($conferenceCall->voice_recording_path)
                                <audio controls class="w-100">
                                    <source src="{{ asset('storage/' . $conferenceCall->voice_recording_path) }}"
                                        type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            @else
                                <span class="badge bg-secondary px-3 py-2">No voice recording uploaded</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold">Attachments</label>
                        <div class="p-3 bg-light border rounded-3 shadow-sm text-center">
                            @php
                                $attachments = $conferenceCall->attachments
                                    ? json_decode($conferenceCall->attachments, true)
                                    : [];
                            @endphp

                            @if (!empty($attachments))
                                <div class="d-flex flex-wrap gap-3 justify-content-center">
                                    @foreach ($attachments as $file)
                                        @if (Str::endsWith($file, ['.mp4', '.mov', '.avi']))
                                            <video src="{{ asset('storage/' . $file) }}" controls
                                                class="img-fluid rounded-3" style="max-height: 200px;"></video>
                                        @else
                                            <img src="{{ asset('storage/' . $file) }}" alt="Attachment"
                                                class="img-fluid rounded-3" style="max-height: 200px;">
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="badge bg-secondary px-3 py-2">No attachments uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Back Button --}}
                <div class="mt-5 text-end">
                    <a href="{{ route('customer_service.conference_call.index') }}"
                        class="btn btn-outline-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
