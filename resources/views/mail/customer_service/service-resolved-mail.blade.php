<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Service Request Resolved</title>
</head>

<body>
    <p><strong>Subject:</strong> Ticket No. , Service Request received.</p>

    <p>Hello {{ $data['clientName'] }},</p>

    <p>
        Your issue listed below has been successfully resolved with the following details:
    </p>

    <p>
        <strong>Site Name/साइट का नाम या साइट का पता:</strong> {{ $data['siteName'] }} <br>
        <strong>Your Issue/आपकी समस्या:</strong> {{ $data['issueFaced'] }} <br>
        <strong>Resolution:</strong> {{ $data['resolution_remark'] }}
    </p>

    <p>
        We hope that we successfully resolved your issue, and we kindly request you to please give
        your valuable feedback regarding the resolution provided to you by clicking the Feedback
        link given below, and give us your valuable Rating.
    </p>

    <p>
        <strong>Feedback Link:</strong> <a href="{{ $data['feedback_form_url'] }}" target="_blank">Click Here</a>
    </p>

    <hr>

    <p>नमस्ते {{ $data['clientName'] }},</p>

    <p>
        नीचे सूचीबद्ध आपकी समस्या को निम्नलिखित विवरणों के साथ सफलतापूर्वक हल कर दिया गया है: -
    </p>

    <p>
        <strong>साइट का नाम :</strong> {{ $data['siteName'] }} <br>
        <strong>आपकी समस्या :</strong> {{ $data['issueFaced'] }} <br>
        <strong>टिप्पणी :</strong> {{ $data['resolution_remark'] }}
    </p>

    <p>
        हमें उम्मीद है कि हमने आपकी समस्या का सफलतापूर्वक समाधान कर लिया है और हम आपसे अनुरोध
        करते हैं कि कृपया नीचे दिए गए फीडबैक लिंक पर क्लिक करके आपको प्रदान किए गए समाधान के बारे में
        अपनी बहुमूल्य प्रतिक्रिया दें और हमें अपनी बहुमूल्य रेटिंग दें।
    </p>

    <p>
        <strong>प्रतिक्रिया लिंक :</strong> <a href="{{ $data['feedback_form_url'] }}" target="_blank">Click Here</a>
    </p>

    <br><br>
    <p>
        Best regards, <br>
        Service Coordinator <br>
        Phone No.: {{ $data['phone'] ?? '' }} <br>
        Volks Energie Pvt. Ltd.
    </p>
</body>

</html>
