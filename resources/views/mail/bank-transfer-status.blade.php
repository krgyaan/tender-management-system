<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>
    
    <p>
        The EMD payment request via Bank Transfer
        @if (!empty($data['tenderNo']) && !empty($data['tenderName']))
            for tender no. {{ $data['tenderNo'] }}, tender name {{ $data['tenderName'] }}
        @elseif (!empty($data['tenderNo']))
            for tender no. {{ $data['tenderNo'] }}
        @elseif (!empty($data['tenderName']))
            for tender name {{ $data['tenderName'] }}
        @endif
        @if (!empty($data['status']))
            has been {{ $data['status'] }}.
        @else
            has been processed.
        @endif
    </p>

    <ul>
        @if ($data['status'] == 'Accepted')
            <li>Date and Time of Payment: {{ date('d-m-Y h:i A', strtotime($data['date_time'])) }}</li>
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
