<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    {{ 'Days Outstanding: ' . $data['since'] }}
    <br>
    {{ 'Reminder Number: ' . $data['reminder'] }}
    <br>

    <p>Dear {{ $data['name'] }},</p>

    <p>The status of the Cheque no {{ $data['chequeNo'] }} is {{ $data['status'] }}.</p>

    <p>The details of the cheque submitted are as follows:</p>
    <ul>
        <li>Date: {{ $data['date'] }}</li>
        <li>Cheque no.: {{ $data['chequeNo'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>
    <p>
        The purpose of the cheque has been served, so we request you to either cancel it and attach a photo of the
        cancelled cheque in reply to this mail
        or courier the cheque to our office address given below.
    </p>
    <p>
        Volks Energie Pvt. Ltd.<br>
        B1/D8, 2nd Floor,<br>
        Mohan Cooperative Industrial Estate,<br>
        Mathura Road,<br>
        New Delhi - 110044<br>
        Phone no.: +91-8882591733
    </p>

    <p>
        In case of a courier, please reply to this mail with the Courier Docket Number and the Courier Docket Slip so
        that we can effectively track the envelope
        and ensure it is not lost in transit.
    </p>

    <p>
        Best Regards,<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd.
    </p>
</div>
