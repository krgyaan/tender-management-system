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
        @if(isset($data['status']))
            The Bank Guarantee submitted against 
            {{ isset($data['tenderNo']) ? 'Wo no. ' . $data['tenderNo'] : '' }}, Project name {{ $data['projectName'] ?? 'N/A' }}, has expired.
        @else
            The status of the Tender no. {{ $data['tenderNo'] ?? 'N/A' }}, project name {{ $data['projectName'] ?? 'N/A' }}, is {{ $data['status'] ?? 'Unknown' }}.
        @endif
    </p>

    <p>Please initiate the process of releasing the EMD in the form of BG for Rs. {{ $data['amount'] }} submitted against the tender.</p>
    <p>The details of the EMD in the form of BG submitted are as follows:</p>
    <ul>
        <li>BG no.: {{ $data['bg_no'] }}</li>
        <li>BG Validity: {{ $data['bg_validity'] }}</li>
        <li>BG Claim Period Expiry: {{ $data['bg_claim_period_expiry'] }}</li>
        <li>BG in favor of: {{ $data['favor'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
        <li>Soft copy of the BG: attached below</li>
    </ul>
    <p>Please courier the DD back to our address mentioned below:</p>
    <p>
        Volks Energie Pvt. Ltd.<br>
        B1/D8, 2nd Floor,<br>
        Mohan Cooperative Industrial Estate,<br>
        New Delhi - 110044<br>
        Phone no.: +91-8882591733
    </p>

    <p>Please reply to this mail with the Courier Docket Number and the Courier Docket Slip so that we can effectively
        track the envelope and ensure its preservation in transit.</p>

    <p>
        Best Regards,<br>Accounts team,<br>Volks Energie Pvt. Ltd.
    </p>
</div>
