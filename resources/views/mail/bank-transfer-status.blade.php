<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    <p>
        The EMD payment request via Bank Transfer for tender no. {{ $data['tenderNo'] }}, tender name
        {{ $data['tenderName'] }}, has been {{ $data['status'] }}.
    </p>

    <ul>
        @if ($data['status'] == 'Accepted')
            <li>UTR for the payment: {{ $data['utr'] }}</li>
            <li>UTR Message: {{ $data['utr_message'] }}</li>
            <li>Remarks: {{ $data['remarks'] }}</li>
        @else
            <li>Reason for rejection: {{ $data['reason'] }}</li>
        @endif
    </ul>

    <p>
        Best of luck for {{ $data['status'] == 'Accepted' ? 'the tender ' . $data['tenderNo'] : 'the next tenders' }}
    </p>

    <p>
        Regards,<br>
        Shivani,<br>
        Accounts team.
    </p>
</div>
