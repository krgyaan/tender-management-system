@php
    $messageTypes = [
        'success' => 'success',
        'error' => 'danger',
    ];
@endphp

@foreach ($messageTypes as $type => $class)
    @if (Session::has($type))
        <div class="alert alert-{{ $class }}" role="alert">
            {{ Session::get($type) }}
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="m-0 list-unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
