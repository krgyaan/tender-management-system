<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complaint Registered</title>
</head>

<body>
    <p>Hello {{ $data['clientName'] }},</p>


    <p>
        Your complaint has been registered successfully with
        <strong>Ticket No- {{ $data['ticket_no'] }}</strong>
        with your issue listed below :-
    </p>

    <p>
        <strong>Site Name:</strong> {{ $data['siteName'] }} <br>
        <strong>Issue Faced:</strong> {{ $data['issueFaced'] }}
    </p>

    <p>
        We will get back to you soon on your given contact and resolve the issue as early as possible.
    </p>

    <p>Thank You !</p>

    <hr>

    <p>नमस्ते {{ $data['clientName'] }},</p>

    <p>
        आपकी शिकायत <strong>टिकट नंबर - </strong> के साथ सफलतापूर्वक दर्ज की गई है।<br>
        विवरण नीचे दिया गया है: -
    </p>

    <p>
        <strong>साइट का नाम :</strong> {{ $data['siteName'] }} <br>
        <strong>आपकी समस्या :</strong> {{ $data['issueFaced'] }}
    </p>

    <p>
        हम आपके दिये गए संपर्क पर जल्द ही आपसे संपर्क करेंगे और समस्या का जल्द से जल्द समाधान करेंगे।
    </p>

    <p>धन्यवाद !</p>

    <br><br>
    <p>
        Regards, <br>
        Service Coordinator <br>
        Phone No.: {{ $data['phone'] ?? '' }} <br>
        Volks Energie Pvt. Ltd. <br>
        <!-- {{ $data['address'] ?? '' }} -->
    </p>
</body>

</html>
