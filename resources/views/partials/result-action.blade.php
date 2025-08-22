<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ route('results.show', $tender->id) }}">View</a>
        </li>
        <li>
            <button class="dropdown-item upload-result-btn1" data-tender-id="{{ $tender->id }}">Parties
                Detail</button>
        </li>
        <li>
            <button class="dropdown-item upload-result-btn2" data-tender-id="{{ $tender->id }}">Result
                Detail</button>
        </li>
        @if ($tender->emds && $tender->emds->count() > 0)
            <li>
                <button class="dropdown-item update-emd-status-btn" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvas-emd-status-{{ $tender->id }}"
                    aria-controls="offcanvas-emd-status-{{ $tender->id }}">
                    Update Emd Status
                </button>
            </li>
        @endif
        @if ($tender->status == '25')
            <li>
                <button class="dropdown-item basic-details-btn" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvas-basic-details-{{ $tender->id }}"
                    aria-controls="offcanvas-basic-details-{{ $tender->id }}">
                    Basic Details
                </button>
            </li>
        @endif
    </ul>
    @include('partials.result-offcanvas', [
        'tenderId' => $tender->id,
        'emdId' => $tender?->emds->first()?->id ?? '0',
        'mode' => $tender?->emds->first()?->instrument_type ?? '0',
        'offcanvasId' => "offcanvas-emd-status-{$tender->id}",
    ])
</div>
