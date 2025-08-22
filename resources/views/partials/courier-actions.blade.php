<div class="d-flex gap-2 flex-wrap">
    @if (in_array(Auth::user()->role, ['common-coordinator', 'admin']))
        <a type="button" data-id="{{ $courier->id }}" data-status="{{ $courier->status }}" data-bs-toggle="modal"
            data-bs-target="#statusModal" class="btn btn-primary btn-xs status-btn">
            Status
        </a>
        <a href="{{ route('courier.despatch', $courier->id) }}" class="btn btn-success btn-xs">
            Dispatch
        </a>
        <form action="{{ route('courier.destroy', $courier->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-xs"
                onclick="return confirm('Are you sure you want to delete this courier?')">
                <i class="fa fa-trash"></i>
            </button>
        </form>
    @endif
    <a href="{{ route('courier.show', $courier->id) }}" class="btn btn-info btn-xs">
        View
    </a>
    @if ($courier->emp_from == Auth::user()->id || in_array(Auth::user()->role, ['common-coordinator', 'admin']))
    @endif
</div>
