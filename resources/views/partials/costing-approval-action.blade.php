<a class="btn btn-xs btn-info" href="{{ route('costing-approval.show', $tdr->id) }}">
    View
</a>
<a href="{{ $tdr->sheet->driveid }}" target="_blank" class="btn btn-xs btn-primary">
    Edit Sheet
</a>
<button type="button" class="btn btn-xs btn-secondary" data-bs-toggle="modal" data-bs-target="#approveSheetModal"
    data-tender-id="{{ $tdr->id }}">
    Approve Sheet
</button>
