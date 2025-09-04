<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Service Request</title>
</head>

<body>
    <p>Hello,</p>

    <p>
        You have received a new Service Request with
        <strong>Ticket No- {{ $data['ticket_no'] }}</strong>
        with the following details:-
    </p>

    <p>
        <strong>Client Name/आपका नाम:</strong> {{ $data['clientName'] }} <br>
        <strong>Organization:</strong> {{ $data['organization'] }} <br>
        <strong>Site Name or Site Address/साइट का नाम या साइट का पता:</strong> {{ $data['siteName'] }} <br>
        <strong>Contact No./संपर्क नंबर:</strong> {{ $data['contactNo'] }} <br>
        <strong>Issue Faced/आपकी समस्या:</strong> {{ $data['issueFaced'] }} <br>
        <strong>Photos/समस्या की तस्वीरें:</strong>

    </p>


    <p>
        Please have a call with the customer and find out how the Service request can be closed.
    </p>

    <p>Thank You!</p>

    <br><br>
    <p>
        Regards, <br>
        Service Coordinator <br>
        <!-- Phone No.: {{ $data['phone'] ?? '' }} <br> -->
        Volks Energie Pvt. Ltd. <br>
        <!-- {{ $data['address'] ?? '' }} -->
    </p>
</body>

</html>
