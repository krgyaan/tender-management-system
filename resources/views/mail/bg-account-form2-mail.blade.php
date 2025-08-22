<div>
    <p>Dear {{ $data['assignee'] }},</p>

    <p>The request for BG creation has been {{ $data['status'] }}</p>

    <ul>
        <li>Soft copy of the BG: attached below</li>
        <li>Courier Docket no.: {{ $data['docket_no'] }}</li>
        <li>Courier Provider: {{ $data['courier_provider'] }}</li>
        <li>Courier Docket Slip: attached below</li>
    </ul>

    @if ($data['status'] == 'Accepted')
        <p>SFMS Confirmation of the BG will be sent in the next mail.</p>
        <p>Remarks: {{ $data['remarks'] }}</p>
    @endif

    <p>
        Regards,<br>
        Imran Khan,<br>
        Accounts team.
    </p>

</div>
