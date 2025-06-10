<div>
    <p>Dear {{ $data['assignee'] }},</p>
    <p>The request for Cheque creation has been {{ $data['status'] }}</p>
    @if ($data['status'] == 'Accepted')
        <p>Soft copy of the cheque: attached below</p>
        <p>Remarks: {{ $data['remarks'] ?? '' }} (optional)</p>
    @elseif ($data['status'] == 'Rejected')
        <p>Reason for rejection: {{ $data['reason'] }}</p>
    @endif
    <p>“Ensuring all cheques are cleared on time builds the company's credibility.”</p>
    <p>The cheque has been handed over to the team, the receiving is attached below.</p>
    <p>
        Regards,<br>
        Kailash<br>
        Accounts team.
    </p>
</div>
