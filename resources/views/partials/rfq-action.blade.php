<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('rfq.create', $tender->id) }}" class="btn btn-primary btn-xs">
        Send RFQ
    </a>

    @if ($tender->rfqs)
        <a href="{{ route('rfq.recipient', $tender->rfqs) }}" class="btn btn-success btn-xs">
            Receipt
        </a>
        <a href="{{ route('rfq.show', $tender->rfqs) }}" class="btn btn-info btn-xs">
            View
        </a>
    @endif

    @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account']))
        <form action="{{ route('rfq.destroy', $tender->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-xs"
                onclick="return confirm('Are you sure you want to delete this item?');">
                <i class="fa fa-trash"></i>
            </button>
        </form>
    @endif
</div>
