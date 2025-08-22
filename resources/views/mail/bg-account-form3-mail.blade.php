<div>
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

    <p>Soft copy of the BG: attached below</p>
    <p>Courier Provider: {{ $data['courier_provider'] }}</p>
    <p>Courier Docket no.: {{ $data['docket_no'] }}</p>
    <p>Courier Docket Slip: attached below</p>
    <p>SFMS Confirmation: attached below</p>

    <p>Please track the BG and confirm receipt of the BG and the SFMS confirmation via a reply to this email.</p>

    <div>
        <b>Regards,</b><br>
        Imran Khan,<br>
        +91-88825-91733<br>
        Accounts team,<br>
        Volks Energie Pvt. Ltd.
    </div>
</div>
