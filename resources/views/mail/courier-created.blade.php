<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <h4>Dear Coordinator,</h4>

    <p>Please send the courier as detailed below:</p>
    <ul>
        <li>Courier to: {{ $data['to_name'] }}</li>
        <li>Organization Name: {{ $data['to_org'] }}</li>
        <li>Address: {{ $data['to_addr'] }}</li>
        <li>Pin Code: {{ $data['to_pin'] }}</li>
        <li>Mob No.: {{ $data['to_mobile'] }}</li>
        <li>Courier from: {{ $data['from_name'] }}</li>
        <li>Expected Delivery Date: {{ $data['expected_delivery_date'] }}</li>
        <li>Despatch Urgency: {{ $data['despatch_urgency'] }}</li>
    </ul>

    <p>Please find the soft copy of the documents attached.</p>
    <p>
        Also, update the <a href="{{ route('courier.despatch', $data['id']) }}">courier despatch form</a>, when the courier has been
        sent.
    </p>

    <p>
        <b>Regards,</b><br>
        {{ $data['from_name'] }}
    </p>
</div>
