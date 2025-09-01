<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Feedback</title>
</head>
<body>
    <p>Sir,</p>

    <p>
        Thank you for resolving the Service Request with 
        <strong>Ticket No- {{ $data['call_no'] }}</strong>
    </p>

    <p>
        ● <strong>Has the problem been resolved:</strong> {{ $data['resolved'] }} <br>
        ● <strong>Are you satisfied with the services provided:</strong> {{ $data['satisfaction'] }} <br>
        ● <strong>Customer service rating given:</strong> {{ $data['rating'] }} / 10 <br>
        ● <strong>Suggestions:</strong> {{ $data['suggestions'] ?? 'N/A' }}
    </p>

    <p>Thank You!</p>

    <br><br>
    <p>
        Regards, <br>
        {{ $data['customer_name'] }} <br>
        {{ $data['organization'] }} <br>
        Phone No.: {{ $data['phone'] }} <br>
        Email: {{ $data['email'] }}
    </p>
</body>
</html>
