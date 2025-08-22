@if ($info->phydocs)
    <a href="{{ route('phydocs.show', $info->phydocs->id) }}" class="btn btn-primary btn-xs">
        View
    </a>
@endif
<a href="{{ route('phydocs.edit', $info->id) }}" class="btn btn-info btn-xs">
    Submit Docs
</a>
@if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator' || Auth::user()->role == 'account')
    <form action="{{ route('phydocs.destroy', $info->id) }}" method="POST" style="display: inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-xs"
            onclick="return confirm('Are you sure you want to delete this item?');">
            Delete
        </button>
    </form>
@endif
