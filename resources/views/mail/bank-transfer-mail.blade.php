<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear Shivani/Accounts team,</p>

    <p>
        Please make the following payment via Bank Transfer against EMD for tender no.{{ $data['tenderNo'] }},
        Project Name {{ $data['tenderName'] }}, The due date and time of this tender is {{ $data['dueDate'] }} at
        {{ $data['dueTime'] }}.
    </p>

    <p>Please do the Maker using the details below:</p>
    <ul>
        <li>Account Name: {{ $data['bt_acc_name'] }}</li>
        <li>Account Number: {{ $data['bt_acc'] }}</li>
        <li>IFSC Code: {{ $data['bt_ifsc'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
    </ul>
    <p>
        Please make the payment before the due date and time and share the UTR with us on time using the link below or
        your dashboard:
        <a href="{{ $data['utr'] }}">click here</a>
    </p>
    <p>“Ensuring timely operations is the only source of growth”</p>

    <p>
        Regards,<br>
        {{ $data['assignee'] }},<br>
        Approved by {{ $data['tlName'] }}
    </p>
</div>
