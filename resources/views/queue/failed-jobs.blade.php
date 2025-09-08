@extends('layouts.app')
@section('page-title', 'Failed Jobs Management Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('queue.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">← Back</a>
                <h4 class="mb-0 d-inline">Failed Jobs</h4>
            </div>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('queue.retry-all') }}" class="btn btn-success btn-sm"
                    onclick="return confirm('Retry all failed jobs?')">Retry All</a>
                <a href="{{ route('queue.clear-all') }}" class="btn btn-danger btn-sm"
                    onclick="return confirm('Delete all failed jobs permanently?')">Clear All</a>
            </div>
        </div>

        @include('partials.messages')

        <div class="card">
            <div class="card-body">
                @if ($failedJobs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Job Details</th>
                                    <th>Queue</th>
                                    <th>Failed At</th>
                                    <th>Exception</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($failedJobs as $job)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">
                                                {{ json_decode($job->payload, true)['displayName'] ?? 'Unknown Job' }}</div>
                                            <small
                                                class="text-muted font-monospace">{{ substr($job->uuid, 0, 13) }}...</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $job->queue }}</span></td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($job->failed_at)->format('M j, Y') }}<br>
                                                {{ \Carbon\Carbon::parse($job->failed_at)->format('g:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;">
                                                <small
                                                    class="text-danger">{{ substr(strip_tags($job->exception), 0, 100) }}...</small>
                                            </div>
                                            <button class="btn btn-link btn-sm p-0 text-muted" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#exception-{{ $job->id }}"
                                                aria-expanded="false">
                                                <small>Show full exception</small>
                                            </button>
                                            <div class="collapse mt-2" id="exception-{{ $job->id }}">
                                                <div class="card card-body">
                                                    <pre class="mb-0 small">{{ $job->exception }}</pre>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('queue.retry-job', $job->uuid) }}"
                                                    class="btn btn-outline-success" title="Retry Job">
                                                    <i class="fa fa-redo"></i>
                                                </a>
                                                <a href="{{ route('queue.delete-job', $job->uuid) }}"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('Delete this job permanently?')"
                                                    title="Delete Job">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="mb-3">
                        <svg width="48" height="48" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </div>
                    <h5>All Clear!</h5>
                    <p class="mb-0">No failed jobs found.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
