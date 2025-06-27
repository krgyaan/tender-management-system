<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('bg-action', $bg->id) }}">Status</a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('emds-dashboard.show', $bg->emds->id) }}">View</a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('emds-dashboard.edit', $bg->emds->id) }}">Edit</a>
        </li>
        <li>
            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator')
                <form action="{{ route('emds-dashboard.destroy', $bg->emds->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item"
                        onclick="return confirm('Are you sure you want to delete this emd?');">
                        Delete
                    </button>
                </form>
            @endif
        </li>
    </ul>
</div>
