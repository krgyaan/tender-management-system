<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('customer_service.show', $complaint->id) }}">
                View
            </a>
        </li>
        {{-- @if (!$complaint->serviceEngineer) --}}
        @if (in_array(Auth::user()->role, ['coordinator', 'admin']))
            <li>
                <a class="dropdown-item" id="allotServiceEngineerBtn" href="#" data-bs-toggle="modal"
                    data-bs-target="#allotEngineerModal" data-complaint-id="{{ $complaint->id }}">
                    Allot Service Engineer
                </a>
            </li>
        @endif
        {{-- @endif --}}
    </ul>
</div>
