<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('bt-action', $bt->id) }}" class="btn btn-sm btn-primary">
        Status
    </a>
    @if (Auth::user()->role == 'admin')
        <button type="button" class="btn btn-sm btn-danger"
            onclick="if(confirm('Are you sure?')) document.getElementById('delete-form-{{ $bt->id }}').submit()">
            <i class="fas fa-trash"></i>
        </button>
        <form id="delete-form-{{ $bt->id }}" action="{{ route('emds-dashboard.destroy', $bt->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
