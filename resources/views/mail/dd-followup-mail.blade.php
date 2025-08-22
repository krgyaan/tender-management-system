<div style="font-family: Arial, sans-serif;">
    {{ 'Days Outstanding: ' . $data['since'] }}
    <br>
    {{ 'Reminder Number: ' . $data['reminder'] }}
    <br>

    <p>Dear {{ $data['name'] }},</p>

    <p>
        The status of the Tender no. {{ $data['tenderNo'] }}, project name {{ $data['projectName'] }}, is
        {{ $data['status'] }}. Please initiate the process of releasing the EMD, Rs. {{ $data['amount'] }} submitted
        against the tender. The details of the EMD submitted are as follows:
    </p>

    <ul>
        <li>Date: {{ $data['date'] }}</li>
        <li>DD no.: {{ $data['ddNo'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>

    <p>Please transfer the amount to the below-mentioned account details:</p>
    <ul>
        <li>Account Name: Volks Energie Pvt. Ltd.</li>
        <li>Account No.: {{ $data['accountNo'] }}</li>
        <li>IFSC Code: {{ $data['ifscCode'] }}</li>
    </ul>

    <p>Also please find the Cancelled cheque and the bank-approved mandate form as reference.</p>

    <p>Else, please courier the DD back to our address mentioned below:</p>
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
        that we can effectively track the envelope and ensure it is not lost in transit.
    </p>

    <p>
        Best Regards,<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd.
    </p>
</div>
