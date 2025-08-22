<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('enquiries.show', $enquiry->id) }}">
                View Enquiry
            </a>
        </li>
        @if (optional($enquiry->siteVisits)->isEmpty())
            <li>
                <button class="dropdown-item allocate-visit-btn" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#allocateSiteVisitPanel" data-enquiry-id="{{ $enquiry->id }}">
                    Allocate Site Visit
                </button>
            </li>
        @endif
        @if (optional($enquiry->siteVisits)->isNotEmpty())
            <li>
                <button class="dropdown-item record-visit-btn" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#siteVisitDetailsPanel" data-visit-id="{{ $enquiry->siteVisits->first()->id }}"
                    data-enquiry-id="{{ $enquiry->id }}">
                    Site Visit Details
                </button>
            </li>
        @endif
        @if ($enquiry?->costingSheets ?? false)
            <li>
                <a class="dropdown-item" href="{{ $enquiry->costingSheets->first()->sheet_url }}" target="_blank">
                    Open Costing Sheet
                </a>
            </li>
            <li>
                <button class="dropdown-item submit-costing-btn" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#submitCostingSheetPanel" data-enquiry-id="{{ $enquiry->id }}"
                    data-costing-id="{{ $enquiry->costingSheets->first()->id }}">
                    Submit Costing Sheet
                </button>
            </li>
        @else
            <li>
                <form method="POST" action="{{ route('private-costing-sheet.create') }}">
                    @csrf
                    <input type="hidden" name="title" value="{{ $enquiry->enq_name }}">
                    <input type="hidden" name="id" value="{{ $enquiry->id ?? '' }}">
                    <button type="submit" class="dropdown-item">Create Private Sheet</button>
                </form>
            </li>
        @endif
    </ul>
</div>
