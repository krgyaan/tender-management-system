<div style="font-family: Arial, sans-serif;">
    <p>Dear Kailash/Accounts team,</p>
    <p>Please prepare the following cheque.</p>
    <p>The purpose of the cheque is <strong>{{ $data['purpose'] }}</strong></p>
    <p>Please prepare the cheque using the details below:</p>
    <ul>
        <li>Party Name: {{ $data['partyName'] ?? '' }}</li>
        <li>Cheque Date: {{ $data['chequeDate'] ?? '' }}</li>
        <li>Amount: Rs. {{ $data['amount'] ?? '' }}</li>
    </ul>
    <p>This cheque is required within {{ $data['time_limit'] }} Hrs.</p>
    <p>Please make the cheque within the shared time limit and share the Soft copy of the cheque and the Positive pay
        confirmation, if applicable, and receiving from our team (Format attached) with us on time using the link below
        or your dashboard:</p>
    <a href="{{ $data['link'] }}">Accounts Form (Cheque)</a>
    <p>“Ensuring timely operations is the only source of growth”</p>
    <p>
        Regards,<br>
        {{ $data['assignee'] }},<br>
        Approved by {{ $data['tlName'] }}
    </p>
</div>
