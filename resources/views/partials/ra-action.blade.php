<div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
    <a href="{{ route('ra.show', $tdr->id) }}" class="btn btn-xs btn-info">View</a>
    <a href="#" class="btn btn-xs btn-primary schedule-ra" data-bs-toggle="modal" data-bs-target="#scheduleRAModal"
        data-ra-id="{{ $tdr->id }}" data-tender="{{ $tdr->id }}">Schedule RA</a>
    <a href="#" class="btn btn-xs btn-secondary upload-ra" data-bs-toggle="modal" data-bs-target="#uploadRAModal"
        data-ra-id="{{ $tdr->id }}" data-tender="{{ $tdr->id }}">Upload RA Result</a>
</div>
