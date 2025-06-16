<div>
    <p>Dear Shivani/Accounts team,</p>

    <p>Please make the following payment via Bank Transfer against Tender Fees for tender no. {{ $data['tenderNo'] }},
        Tender Name {{ $data['tenderName'] }},</p>

    <p>The due date and time of this tender is {{ $data['dueDate'] }}.</p>

    <p>Please do the Maker using the details below:</p>
    <ul>
        <li>Account Name: {{ $data['acc_name'] }}</li>
        <li>IFSC Code: {{ $data['ifsc'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>

    <p>Please make the payment before the due date and time and share the UTR with us on time using the link below or
        your dashboard:</p>
    <p><a href="{{ $data['link'] }}">click here</a></p>

    <p>“Ensuring timely operations is the only source of growth”</p>

    <p>Regards,</p>
    <p>{{ $data['assignee'] }},</p>
    <p>Approved by {{ $data['tlName'] }}</p>
</div>
