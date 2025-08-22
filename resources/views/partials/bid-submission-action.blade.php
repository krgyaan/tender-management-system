<a class="btn btn-xs btn-info" href="{{ route('bs.show', $tdr->id) }}">View</a>
<button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#bidSubmittedModal"
    data-tender-id="{{ $tdr->id }}">
    Submit Bid
</button>
<button type="button" class="btn btn-xs btn-secondary" data-bs-toggle="modal" data-bs-target="#tenderMissedModal"
    data-tender-id="{{ $tdr->id }}">
    Tender Missed
</button>
