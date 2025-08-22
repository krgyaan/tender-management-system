<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    <div>
        The EMD payment request via NEFT/RTGS for tender no. {{ $data['tenderNo'] }},
        project name {{ $data['tenderName'] }}, has been {{ $data['status'] }}
    </div>

    <ul>
        @if ($data['status'] == 'Accepted')
            <li>UTR for the payment: {{ $data['utr'] }}</li>
            <li>Remarks: {{ $data['remarks'] }}</li>
        @else
            <li>Reason for rejection: {{ $data['reason'] }}</li>
        @endif
    </ul>

    <p>
        Best of luck for @if ($data['status'] == 'Accepted')
            {{ 'the tender ' . $data['tenderNo'] }}
        @else
            next tender
        @endif
    </p>

    <p>
        <b>Regards,</b><br>
        Shivani,<br>
        Accounts team.
    </p>
</div>
