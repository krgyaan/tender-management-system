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
    <p>
        The status of the Tender no. {{ $data['tenderNo'] }}, tender name {{ $data['projectName'] }}, is
        {{ $data['status'] }}.
        Please initiate the process of releasing the EMD, Rs. {{ $data['amount'] }} submitted against the tender.
        The details of the EMD submitted are as follows:
    </p>

    <ul>
        <li>Date: {{ $data['date'] }}</li>
        <li>UTR no.: {{ $data['utr'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>

    <p>Please transfer the amount to the below-mentioned account details:</p>
    <ul>
        <li>Account no.: Volks Energie Pvt. Ltd.</li>
        <li>Account No.: 003084600002011</li>
        <li>IFSC Code: YESB0000030</li>
    </ul>
    <p>Also please find the Cancelled cheque and the bank-approved mandate form as reference.</p>

    <p>
        Best Regards,<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd
    </p>

</div>
