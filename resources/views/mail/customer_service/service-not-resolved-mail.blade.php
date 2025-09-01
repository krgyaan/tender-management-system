<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Issue Not Resolved</title>
</head>

<body>
    <p>Hello {{ $data['clientName'] }},</p>

    <p>
        Your issue listed below has not been successfully resolved with the following details:
    </p>

    <p>
        <strong>Site Name:</strong> {{ $data['siteName'] }} <br>
        <strong>Your Issue:</strong> {{ $data['issueFaced'] }} <br>
        <strong>Reasons for non-resolution:</strong> {{ $data['resolution_remark'] }}
    </p>

    <p>
        Our team is working tirelessly to resolve the issue at your site.
        Please wait while we resolve the issue.
    </p>

    <br>

    <p>Best Regards,</p>
    <p>
        Service Coordinator <br>
        Phone No.: {{ $data['phone'] ?? '' }} <br>
        Volks Energie Pvt. Ltd.
    </p>

    <hr>

    <p>नमस्ते {{ $data['clientName'] }},</p>

    <p>
        नीचे सूचीबद्ध आपकी समस्या का समाधान नहीं हो पाया है, जिसके विवरण इस प्रकार हैं:
    </p>

    <p>
        <strong>साइट का नाम :</strong> {{ $data['siteName'] }} <br>
        <strong>आपकी समस्या :</strong> {{ $data['issueFaced'] }} <br>
        <strong>समाधान न होने के कारण :</strong> {{ $data['resolution_remark'] }}
    </p>

    <p>
        हमारी टीम आपकी साइट पर समस्या का समाधान करने के लिए अथक प्रयास कर रही है।
        कृपया समस्या के समाधान तक प्रतीक्षा करें।
    </p>

    <p>धन्यवाद !</p>

    <br>

    <p>
        Service Coordinator <br>
        Phone No.: {{ $data['phone'] ?? '' }} <br>
        Volks Energie Pvt. Ltd.
    </p>
</body>

</html>
