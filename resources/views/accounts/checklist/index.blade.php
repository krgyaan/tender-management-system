@extends('layouts.app')
@section('page-title', 'Account Checklists')
@php use Illuminate\Support\Str; @endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('checklists.create') }}" class="btn btn-primary btn-sm">+ Add New Checklist</a>
                    </div>
                @endif
                <div class="card">
                    @include('partials.messages')
                    <div class="card-body">
                        <div class="table-responsive">
                            @if (in_array($userRole, ['admin', 'coordinator']))
                                <!-- Admin/Coordinator Accordion View -->
                                <div class="accordion" id="checklistsAccordion">
                                    @forelse($groupedChecklists as $responsibleId => $userChecklists)
                                        @php
                                            $responsibleUser = $userChecklists->first()->responsibleUser ?? null;
                                            $accordionId = "accordion-$responsibleId";
                                            $tableId = "table-$responsibleId";
                                        @endphp

                                        <div class="accordion-item">
                                            <div class="accordion-header" id="heading-{{ $responsibleId }}">
                                                <div class="d-flex justify-content-between w-100 pe-3 align-items-center">
                                                    <button class="accordion-button collapsed flex-grow-1 text-start" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}"
                                                        aria-expanded="false" aria-controls="{{ $accordionId }}"
                                                        style="background: none; border: none; box-shadow: none;">
                                                        <span>
                                                            {{ $responsibleUser->name ?? 'Unassigned' }}
                                                            <span class="badge bg-info ms-2">
                                                                Total Tasks: {{ $userChecklists->count() }}
                                                            </span>
                                                        </span>
                                                    </button>
                                                    <div class="ms-3">
                                                        <a href="checklists/report/{{ $responsibleId }}"
                                                            class="btn btn-sm btn-outline-success me-2">
                                                            Report
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="{{ $accordionId }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading-{{ $responsibleId }}" data-bs-parent="#checklistsAccordion">
                                            <div class="accordion-body py-4">
                                                <table id="{{ $tableId }}" class="table-hover mb-0 newTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Task Name</th>
                                                            <th>Frequency</th>
                                                            <th>Responsible</th>
                                                            <th>Accountable</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($userChecklists as $checklist)
                                                            <tr>
                                                                <td>{{ $checklist->task_name }}</td>
                                                                <td>{{ $checklist->frequency }}</td>
                                                                <td>{{ $checklist->responsibleUser->name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $checklist->accountableUser->name ?? 'N/A' }}
                                                                </td>
                                                                <td style="white-space: nowrap;">
                                                                    @if ($userId == $checklist->responsibility)
                                                                        <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal"
                                                                            data-bs-target="#respModal{{ $checklist->id }}">
                                                                            Responsibility
                                                                        </button>
                                                                    @endif
                                                                    @if ($userId == $checklist->accountability)
                                                                        <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="modal"
                                                                            data-bs-target="#acctModal{{ $checklist->id }}">
                                                                            Accountability
                                                                        </button>
                                                                    @endif
                                                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                        <a href="{{ route('checklists.edit', $checklist->id) }}"
                                                                            class="btn btn-sm btn-info">Edit</a>
                                                                        <form action="{{ route('checklists.destroy', $checklist->id) }}"
                                                                            method="POST" style="display:inline;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this checklist?')">Delete</button>
                                                                        </form>
                                                                    @endif
                                                                    <button class="btn btn-secondary btn-sm" type="button"
                                                                        data-bs-toggle="offcanvas"
                                                                        data-bs-target="#offcanvas-task-{{ $checklist->id }}"
                                                                        aria-controls="offcanvas-task-{{ $checklist->id }}">
                                                                        Details
                                                                    </button>
                                                                    @include('partials.account-checklist-offcanvas', ['checklistId' => $checklist->id, 'offcanvasId' => 'offcanvas-task-' . $checklist->id])
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-info">No checklists found.</div>
                                    @endforelse
                                </div>
                            @else
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    <a href="checklists/report/{{ $userId }}" class="btn btn-primary btn-sm">
                                        See Report
                                    </a>
                                </div>
                                <!-- User view: show two tabs: My Responsibility and My Accountability -->
                                <ul class="nav nav-tabs mb-3" id="checklistTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="resp-tab" data-bs-toggle="tab" data-bs-target="#resp" type="button" role="tab" aria-controls="resp" aria-selected="true">My Responsibility</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="acct-tab" data-bs-toggle="tab" data-bs-target="#acct" type="button" role="tab" aria-controls="acct" aria-selected="false">My Accountability</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="checklistTabsContent">
                                    <div class="tab-pane fade show active" id="resp" role="tabpanel" aria-labelledby="resp-tab">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Due Date</th>
                                                    <th>Timer</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            @if($userTasksResponsibility && count($userTasksResponsibility))
                                                <tbody>
                                                    @foreach($userTasksResponsibility as $task)
                                                        <tr>
                                                            <td>{{ $task->checklist->task_name ?? '-' }}</td>
                                                            <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y, h:i A') : '-' }}</td>
                                                            <td>
                                                                @php
                                                                    $timerEnd = $task->due_date;
                                                                    $remaining = $timerEnd ? (now()->diffInSeconds(\Carbon\Carbon::parse($timerEnd), false)) : null;
                                                                @endphp
                                                                @if($remaining !== null)
                                                                    <span class="timer" data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td style="white-space: nowrap;">
                                                                <button class="btn btn-secondary btn-sm" type="button"
                                                                    data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvas-task-{{ $task->checklist_id }}"
                                                                    aria-controls="offcanvas-task-{{ $task->checklist_id }}">
                                                                    Details
                                                                </button>
                                                                @include('partials.account-checklist-offcanvas', ['checklistId' => $task->checklist_id, 'offcanvasId' => 'offcanvas-task-' . $task->checklist_id])
                                                                @if($task->responsible_user_id == $userId)
                                                                    <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal"
                                                                        data-bs-target="#respModal{{ $task->id }}">
                                                                        Responsibility
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="acct" role="tabpanel" aria-labelledby="acct-tab">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Responsible User</th>
                                                    <th>Responsibility Completed At</th>
                                                    <th>Responsibility Remark</th>
                                                    <th>Result File</th>
                                                    <th>Timer</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            @if($userTasksAccountability && count($userTasksAccountability))
                                                <tbody>
                                                    @foreach($userTasksAccountability as $task)
                                                        <tr>
                                                            <td>{{ $task->checklist->task_name ?? '-' }}</td>
                                                            <td>{{ $task->checklist->responsibleUser->name ?? '-' }}</td>
                                                            <td>{{ $task->resp_completed_at ? \Carbon\Carbon::parse($task->resp_completed_at)->format('d M Y, h:i A') : '-' }}</td>
                                                            <td>{{ $task->resp_remark ?? '-' }}</td>
                                                            <td>
                                                                @if($task->resp_result_file)
                                                                    <a href="{{ asset('checklist/' . $task->resp_result_file) }}" target="_blank">View</a>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    // Adjust due_date: if it's Saturday, add 2 days; else add 1 day
                                                                    $timerEnd = \Carbon\Carbon::parse($task->due_date);
                                                                    if ($timerEnd->isSaturday()) {
                                                                        $timerEnd->addDays(2);
                                                                    } else {
                                                                        $timerEnd->addDay();
                                                                    }
                                                                    $remaining = now()->diffInSeconds($timerEnd, false);
                                                                @endphp
                                                                @if($remaining !== null)
                                                                    <span class="timer" data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td style="white-space: nowrap;">
                                                                <button class="btn btn-secondary btn-sm" type="button"
                                                                    data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvas-task-{{ $task->checklist_id }}"
                                                                    aria-controls="offcanvas-task-{{ $task->checklist_id }}">
                                                                    Details
                                                                </button>
                                                                @include('partials.account-checklist-offcanvas', ['checklistId' => $task->checklist_id, 'offcanvasId' => 'offcanvas-task-' . $task->checklist_id])
                                                                @if(!$task->acc_completed_at && $task->resp_completed_at)
                                                                    <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="modal"
                                                                        data-bs-target="#acctModal{{ $task->id }}">
                                                                        Accountability
                                                                    </button>
                                                                @elseif(!$task->acc_completed_at && !$task->resp_completed_at)
                                                                    <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="tooltip" title="Waiting for responsible user to complete">
                                                                        Accountability
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Modal templates -->
    @if ($checklists->count())
        @if (
                in_array(Auth::user()->role, ['admin', 'coordinator']) &&
                $checklists->first() instanceof \Illuminate\Support\Collection
            )
            @foreach ($userTasksAccountability as $group)
                @foreach ($group as $checklist)
                    @include('partials.account-checklist-modal', ['checklist' => $checklist])
                @endforeach
            @endforeach
        @else
            @foreach ($userTasksResponsibility as $checklist)
                @include('partials.account-checklist-modal', ['checklist' => $checklist])
            @endforeach
        @endif
    @endif
@endsection

@push('styles')
    <style>
        .newTable {
            width: 100%;
            border-collapse: collapse;
        }

        .newTable th {
            font-weight: bold;
            text-transform: uppercase
        }

        .newTable th,
        .newTable td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.timer').forEach(function(el) {
            if (typeof startCountdown === 'function') {
                startCountdown(el);
            }
        });
    });
</script>
@endpush
