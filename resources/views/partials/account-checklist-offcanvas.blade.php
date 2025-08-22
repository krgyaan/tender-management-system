@php
    $checklist = \App\Models\Checklist::with(['responsibleUser', 'accountableUser'])->find($checklistId);
@endphp
<div class="offcanvas offcanvas-start" tabindex="-1" id="{{ $offcanvasId }}" aria-labelledby="{{ $offcanvasId }}-label">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="{{ $offcanvasId }}-label">Task Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @if($checklist)
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Task Name:</strong> <br>
                    {{ $checklist->task_name }}
                </li>
                <li class="list-group-item"><strong>Frequency:</strong> <br>
                    {{ $checklist->frequency }}
                </li>
                <li class="list-group-item"><strong>Description:</strong> <br>
                    {!! nl2br(wordwrap($checklist->description, 50, "\n")) ?? '-' !!}
                </li>
                <li class="list-group-item"><strong>Responsible:</strong> <br>
                    {{ $checklist->responsibleUser->name ?? '-' }}
                </li>
                <li class="list-group-item"><strong>Accountable:</strong> <br>
                    {{ $checklist->accountableUser->name ?? '-' }}
                </li>
            </ul>
        @else
            <div class="alert alert-warning">Checklist details not found.</div>
        @endif
    </div>
</div>
