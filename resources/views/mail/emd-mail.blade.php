<x-mail::message>
    # EMD Raised Successfully.

    Project Name: {{ $emd['project'] }}
    Instrument Type: {{ $emd['instrument'] }}

    EMD Requested By: {{ $emd['requested_by'] }}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
