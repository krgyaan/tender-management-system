<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    <p>The EMD payment request via Pay on Portal has been {{ $data['status'] }}.</p>
    @if ($data['status'] == 'Accepted')
        <p>UTR for the payment: {{ $data['utr'] }}</p>
        <p>Date and Time of Payment: {{ date('d-m-Y h:i A', strtotime($data['date_time'])) }}</p>
        <p>UTR Message: {{ $data['utr_msg'] }}</p>
        <p>Remarks: {{ $data['remarks'] }}</p>
    @else
        <p>Reason for rejection: {{ $data['reason'] }}</p>
    @endif
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
