<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('enquiries.show', $enquiry->id) }}">
                View Details
            </a>
        </li>
        @if ($enquiry->costingSheets->first())
            <li>
                <a class="dropdown-item" href="{{ $enquiry->costingSheets->first()->sheet_url }}" target="_blank">
                    Open Costing Sheet
                </a>
            </li>
        @else
            <li>
                <p class="dropdown-item">Costing Sheet Not Available</p>
            </li>
        @endif
        <li>
            <button class="dropdown-item submit-quote-btn" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#quoteSubmissionPanel" data-enquiry-id="{{ $enquiry->id }}">
                Quote Submission
            </button>
        </li>
        <li>
            <button class="dropdown-item drop-quote-btn" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#quotationDroppedPanel" data-enquiry-id="{{ $enquiry->id }}">
                Quotation Dropped
            </button>
        </li>
    </ul>
</div>
