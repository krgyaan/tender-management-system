<a href="{{ route('dd-action', $dd->id) }}"
    class="btn btn-xs btn-primary {{ !optional($dd->ddChq)->action ? 'disabled' : '' }}">
    Status
</a>
<a href="{{ route('emds-dashboard.show', $dd->emd->id) }}" class="btn btn-xs btn-info">
    View
</a>
<a href="{{ route('emds-dashboard.edit', $dd->emd->id) }}" class="btn btn-xs btn-warning">
    Edit
</a>
@if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
    <form action="{{ route('emds-dashboard.destroy', $dd->emd->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-xs btn-danger"
            onclick="return confirm('Are you sure you want to delete this emd?');">
            Delete
        </button>
    </form>
@endif
