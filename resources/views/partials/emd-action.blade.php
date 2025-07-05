<div @class(['d-flex', 'flex-wrap', 'gap-2'])>
    @if ($tender->emds->count() > 0)
        <a href="{{ route('emds.edit', $tender->id) }}" class="btn btn-primary btn-xs">
            Edit
        </a>
        <a href="{{ route('emds.show', $tender->id) }}" class="btn btn-xs btn-info">
            View
        </a>
    @else
        <a href="{{ route('emds.create', base64_encode($tender->tender_no)) }}" class="btn btn-info btn-xs">
            Request EMD
        </a>
    @endif
    @if (
        $tender->gst_values > 0 &&
            $tender->emds->isNotEmpty() &&
            in_array($tender->emds->first()->instrument_type, [1, 5, 6]))
        <a class="btn btn-secondary btn-xs" href="{{ route('tender-fees.create', $tender->emds->first()->id) }}">
            Tender Fees
        </a>
    @endif
</div>
