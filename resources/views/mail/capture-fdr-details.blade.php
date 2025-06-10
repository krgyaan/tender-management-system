<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear sir,</p>

    <p>The following Bank guarantee has been issued for your organization.</p>
    <ul>
        <li>BG no.*: {{ $data['bg_no'] }}</li>
        <li>BG Expiry Date: {{ $data['expiry_date'] }}</li>
        <li>BG Claim Date: {{ $data['claim_date'] }}</li>
        <li>BG in favor of: {{ $data['favor'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
        <li>BG Stamp paper value: Rs. {{ $data['bg_stamp'] }}</li>
    </ul>

    <ul>
        <p>Soft copy of the BG: attached below</p>
        <li>Courier Provider: {{ $data['courier_provider'] }}</li>
        <li>Courier Docket no.: {{ $data['docket_no'] }}</li>
        <li>Courier Docket Slip: attached below</li>
        <li>SFMS Confirmation: attached below</li>
    </ul>

    <p>Please track the BG and confirm receipt of the BG and the SFMS confirmation via a reply to this email.</p>

    <div>
        <b>Regards,</b><br>
        Imran Khan,<br>
        +91-88825-91733<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd.
    </div>
</div>
