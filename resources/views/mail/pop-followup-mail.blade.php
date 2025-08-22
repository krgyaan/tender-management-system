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
        The status of the Tender no. {{ $data['tenderNo'] }}, project name {{ $data['projectName'] }}, is
        {{ $data['status'] }}.
        Please initiate the process of releasing the EMD, Rs. {{ $data['amount'] }} submitted against the tender.
    </p>

    <p>
        The details of the EMD submitted are as follows:
    </p>

    <ul>
        <li>Date: {{ $data['date'] }}</li>
        <li>UTR no.: {{ $data['utr'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>

    <p>
        Please transfer the amount to the below-mentioned account details:
    </p>

    <ul>
        <li>Account no.: Volks Energie Pvt. Ltd.</li>
        <li>Account No: {{ $data['accountNo'] }}</li>
        <li>IFSC Code: {{ $data['ifsc'] }}</li>
    </ul>

    <p>
        Also please find the Cancelled cheque and the bank-approved mandate form as reference.
    </p>

    <p>
        Best Regards,<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd.
    </p>

</div>
