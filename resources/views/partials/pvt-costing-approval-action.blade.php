<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('private-costing-sheet.show', $sheet->id) }}">
                View Details
            </a>
        </li>
        <li>
            <a href="{{ $sheet->sheet_url }}" target="_blank" class="dropdown-item">
                Edit Sheet
            </a>
        </li>
        <li>
            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#approveSheetModal"
                data-tender-id="{{ $sheet->id }}">
                Approve Sheet
            </button>
        </li>
    </ul>
</div>
