<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['from_name'] }},</p>

    <p>The courier given by you has been couriered, the details are as follows.</p>
    <ul>
        <li>Courier Request No.: {{ $data['id'] }}</li>
        <li>Courier Provider: {{ $data['courier_provider'] }}</li>
        <li>Pickup date and time: {{ $data['pickup_date_time'] }}</li>
        <li>Docket No.: {{ $data['docket_no'] }}</li>
    </ul>
    <p>The Docket slip against the tender has been attached with the mail.</p>
    <p>
        <b>Regards,</b><br>
        {{ $data['coordinator_name'] }}
    </p>
</div>
