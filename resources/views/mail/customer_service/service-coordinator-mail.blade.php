<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Service Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            padding: 20px;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #004085;
        }

        .details {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fdfdfd;
        }

        .details p {
            margin: 8px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>New Service Request Received</h2>
        <p>Sir,</p>
        <p>You have received a new Service Request with <b>Ticket No -{{ $data['ticket_no'] }}</b> with the following
            details:</p>

        <div class="details">
            <p><b>Client Name/आपका नाम:</b> {{ $data['clientName'] }}</p>
            <p><b>Organization/संगठन:</b> {{ $data['organization'] }}</p>
            <p><b>Site Name or Site Address/साइट का नाम या साइट का पता:</b> {{ $data['siteName'] }}</p>
            <p><b>Contact No./संपर्क नंबर:</b> {{ $data['contactNo'] }}</p>
            <p><b>Issue Faced/आपकी समस्या:</b> {{ $data['issueFaced'] }}</p>
        </div>

        <p>Kindly allot the Site engineer by using the <b>"Allotment of Site Engineer"</b> option.</p>

        <div class="footer">
            <p>Thank You!</p>
            <p>Regards, <br>Coordinator,<br>Volks Energie Pvt. Ltd</p>
        </div>
    </div>
</body>

</html>
