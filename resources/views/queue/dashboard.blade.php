@extends('layouts.app')
@section('page-title', 'Queue Management Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Queue Dashboard</h4>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('queue.process') }}" class="btn btn-primary btn-sm">Process Now</a>
                <a href="{{ route('queue.retry-all') }}" class="btn btn-success btn-sm"
                    onclick="return confirm('Retry all failed jobs?')">Retry All</a>
            </div>
        </div>
        @include('partials.messages')

        <!-- Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <ul class="nav nav-tabs card-header-tabs justify-content-center" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending-tab" type="button">
                            Pending Jobs <span class="badge bg-primary ms-1">{{ $stats['pending_jobs'] }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#failed-tab" type="button">
                            Failed Jobs <span class="badge bg-danger ms-1">{{ $stats['failed_jobs'] }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Pending Jobs Tab -->
                    <div class="tab-pane fade show active" id="pending-tab">
                        @if ($pendingJobs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="">
                                        <tr>
                                            <th>Job</th>
                                            <th>Queue</th>
                                            <th>Attempts</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendingJobs as $job)
                                            <tr>
                                                <td>
                                                    <div>{{ $job['job_name'] }}</div>
                                                    <small class="text-muted">ID: {{ $job['id'] }}</small>
                                                </td>
                                                <td>{{ $job['queue'] }}</td>
                                                <td>{{ $job['attempts'] }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($job['created_at'])->format('d M Y h:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <div class="mb-2">📭</div>
                                <div>No pending jobs</div>
                            </div>
                        @endif
                    </div>

                    <!-- Failed Jobs Tab -->
                    <div class="tab-pane fade" id="failed-tab">
                        @if ($recentFailed->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="">
                                        <tr>
                                            <th>Job</th>
                                            <th>Queue</th>
                                            <th>Failed At</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentFailed as $job)
                                            <tr>
                                                <td>
                                                    <div class="fw-medium">
                                                        {{ json_decode($job->payload, true)['displayName'] ?? 'Unknown Job' }}
                                                    </div>
                                                    <small class="text-muted">{{ $job->exception }}</small>
                                                </td>
                                                <td>{{ $job->queue }}</td>
                                                <td>{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('queue.retry-job', $job->uuid) }}"
                                                            class="btn btn-outline-success btn-sm">Retry</a>
                                                        <a href="{{ route('queue.delete-job', $job->uuid) }}"
                                                            class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Delete this job?')">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($recentFailed->count() >= 10)
                                <div class="border-top p-3 text-center">
                                    <a href="{{ route('queue.failed-jobs') }}"
                                        class="btn btn-outline-secondary btn-sm">View All Failed Jobs</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5 text-muted">
                                <div class="mb-2">✅</div>
                                <div>No failed jobs</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
