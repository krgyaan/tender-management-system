<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Visit Report</title>
</head>
<body>
    <p>Hello,</p>

    <p>
        The service visit was done on <strong>{{ $data['visit_date'] }}</strong>.
    </p>

    <p>
        <strong>The problem has been resolved:</strong><br>
        {{ $data['resolution'] }}
    </p>

    <p>
        <strong>Photos of the site after resolution:</strong><br>
        @if(!empty($data['photos']))
            @foreach($data['photos'] as $photo)
                <a href="{{ $photo }}" target="_blank">View Photo</a><br>
            @endforeach
        @else
            No photos uploaded.
        @endif
    </p>

    <p>
        <strong>Remarks/Reason for non-resolution:</strong><br>
        {{ $data['remarks'] ?? 'N/A' }}
    </p>

    <p>
        Please find the customer signed visit report attached.
    </p>

    <p>Thank You!</p>

    <br><br>
    <p>
        Regards, <br>
        Service Engineer <br>
        Phone No.: {{ $data['phone'] ?? '' }}
    </p>
</body>
</html>
