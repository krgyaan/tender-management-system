<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        @if ($complaint->callDetails)
            <li>
                <a class="dropdown-item" href="{{ route('customer_service.conference_call.show', $complaint->id) }}">
                    View Call Details
                </a>
            </li>
        @else
            <li>
                <a class="dropdown-item" href="{{ route('customer_service.conference_call.create', $complaint->id) }}">
                    Enter Call Details
                </a>
            </li>
        @endif
    </ul>
</div>
