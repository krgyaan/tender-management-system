<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    <p>The request for Cheque creation has been {{ $data['status'] }}.</p>
    @if ($data['status'] == 'Accepted')
        <p>Soft copy of the cheque: attached below.</p>
        <p>Remarks: {{ $data['remark'] }}</p>
    @else
        <p>Reason for rejection:{{ $data['reason'] }}</p>
    @endif
    <p>Ensuring all cheques are cleared on time builds the company's credibility.</p>
    <p>
        <b>Regards,</b><br>
        Tushar,<br>
        Accounts team.
    </p>
</div>
