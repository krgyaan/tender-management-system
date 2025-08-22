<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Resolution Plan</title>
</head>
<body>
    <p>Hello,</p>

    <p>
        As discussed during the conference call with the customer, the issue identified is:  
        <strong>{{ $data['issue_description'] }}</strong>
    </p>

    <p>
        <strong>Actions planned for resolving the issue:</strong><br>
        {{ $data['actions_planned'] }}
    </p>

    <p>
        <strong>Material/Tools required for resolution:</strong><br>
        {{ $data['materials'] }}
    </p>

    <p>
        Please visit the site as per the date and time given by the customer.
    </p>

    <p>Thank You!</p>

    <br><br>
    <p>
        Regards, <br>
        Service Coordinator <br>
        Phone No.: {{ $data['phone'] ?? '' }} <br>
        Volks Energie Pvt. Ltd. <br>
        {{ $data['address'] ?? '' }}
    </p>
</body>
</html>
