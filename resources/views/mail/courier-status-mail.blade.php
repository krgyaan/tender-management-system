<div>
    <p>Dear {{ $data['from_name'] }},</p>
    <p>
        The courier sent to {{ $data['to_name'] }} of {{ $data['to_org'] }} has been {{ $data['status'] }}.
    </p>

    <ul>
        <li>Request No: {{ $data['req_no'] }}</li>
        <li>Courier Provider: {{ $data['provider'] }}</li>
        <li>Docket No: {{ $data['docket_no'] }}</li>
        <li>Pickup date and time: {{ $data['pickup'] }}</li>
        <li>Delivery date and time: {{ $data['delivery'] }}</li>
        <li>Delivery within Expected time: {{ $data['expected'] }}</li>
    </ul>

    <p>The Proof of Delivery (POD) slip against the tender has been attached with the mail.</p>

    <br>
    <p>Regards,<br>{{ $data['coordinator_name'] }}</p>
</div>
