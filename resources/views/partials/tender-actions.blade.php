<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('tender.edit', $tender->id) }}">
                Edit Tender
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('tender.show', $tender->id) }}">
                View Tender
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('tender.info.create', $tender->id) }}">
                Fill Info Sheet
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('extension.create', $tender->id) }}">
                Request Extension
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('submit_query.create', $tender->id) }}">
                Submit Queries
            </a>
        </li>
        @if (in_array(Auth::user()->role, ['coordinator', 'admin']))
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    data-id="{{ $tender->id }}" data-name="{{ $tender->status }}">
                    Change Status
                </a>
            </li>
            <li>
                <a class="dropdown-item text-danger" href="#"
                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();">
                    Delete
                </a>
                <form action="{{ route('tender.destroy', $tender->id) }}" method="POST" id="deleteForm{{ $tender->id }}"
                    style="display: none;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="{{ $tender->id }}">
                </form>
            </li>
        @endif
    </ul>
</div>
