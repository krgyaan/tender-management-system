<div @class(['d-flex', 'flex-wrap', 'gap-2'])>
    @if ($tender->emds->count() > 0)
        <a href="{{ route('emds.edit', $tender->id) }}" class="btn btn-primary btn-xs">
            Edit
        </a>
        <a href="{{ route('emds.show', $tender->id) }}" class="btn btn-xs btn-info">
            View
        </a>
    @else
        <div class="btn-group">
            <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                Request EMD
            </button>
            <ul class="dropdown-menu">
                @foreach ($bi_types as $type => $label)
                    <li>
                        @php
                            $query = [
                                'tender_no' => base64_encode($tender->id),
                                'instrument_type' => $type,
                            ];
                            $routes = ['dd', 'fdr', 'chq', 'bg', 'bt', 'pop'];
                            $url = route($routes[$type - 1] . '.emds.request', $query);
                        @endphp
                        <a class="dropdown-item" href="{{ $url }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
