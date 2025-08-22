<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('lead.edit', $lead->id) }}">
                Edit Lead
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('lead.show', $lead->id) }}">
                View Lead
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('lead.initiateFollowup', $lead->id) }}">
                Initiate Follow-up
            </a>
        </li>
        <li>
            <a class="dropdown-item text-success" href="{{ route('lead.enquiry', $lead->id) }}">
                Enquiry Received
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('lead-allocations.create', $lead->id) }}">
                Allocate to TE
            </a>
        </li>
        @if (in_array(Auth::user()->role, ['admin']))
            <li>
                <a class="dropdown-item text-danger" href="#"
                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $lead->id }}').submit();">
                    Delete
                </a>
                <form action="{{ route('lead.destroy', $lead->id) }}" method="POST" id="deleteForm{{ $lead->id }}"
                    style="display: none;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $lead->id }}">
                </form>
            </li>
        @endif
    </ul>
</div>
