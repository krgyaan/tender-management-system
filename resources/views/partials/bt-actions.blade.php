<div class="d-flex flex-wrap gap-2">
    @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account-executive', 'accountant', 'account-leader']))
        <a class="btn btn-xs btn-primary" href="{{ route('bt-action', $bt->id) }}">
            Status
        </a>
    @endif
    <a href="{{ route('emds-dashboard.show', $bt->emd->id) }}" class="btn btn-xs btn-info">
        View
    </a>
    <a href="{{ route('emds-dashboard.edit', $bt->emd->id) }}" class="btn btn-xs btn-warning">
        Edit
    </a>
    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
        <form action="{{ route('emds-dashboard.destroy', $bt->emd->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-xs btn-danger"
                onclick="return confirm('Are you sure you want to delete this emd?');">
                Delete
            </button>
        </form>
    @endif
</div>
