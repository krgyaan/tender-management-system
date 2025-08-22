<div>
    <p>Dear Imran (Accounts team),</p>

    <p>Please use the pre-filled forms attached below to prepare the following Bank Guarantee.
        The purpose of the Bank Guarantee is {{ $data['purpose'] }},</p>

    <p>Please prepare the BG using the details below:</p>
    <ul>
        <li>BG in favor of: {{ $data['favor'] }}</li>
        <li>BG address: {{ $data['address'] }}</li>
        <li>BG Expiry Date: {{ $data['expiry_date'] }}</li>
        <li>BG Claim Date: {{ $data['claim_date'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }} </li>
        <li>BG Stamp paper value: Rs. {{ $data['bg_stamp'] }} </li>
        <li>BG Format (Tender executive): attached with the mail.</li>
        <li>PO copy/request letter from beneficiary: attached with the mail.</li>
    </ul>

    <ul>
        <li>Beneficiary Account details:</li>
        <li>Beneficiary Name:</li>
        <li>Account No.:</li>
        <li>IFSC code:</li>
    </ul>

    <p>This BG is required within {{ $data['bg_needs'] }} Hrs.</p>

    <p>
        Please courier the BG to the address below keeping sufficient margin for the courier delivery time to ensure
        the BG reaches before the due date and time (in case of tender) and share the SOFT COPY of the BG and the
        Docket slip along with SFMS confirmation with us on time using the link below or your dashboard:
    </p>
    {{ $data['link'] }}
    <p>Courier Address: {{ $data['courier_addr'] }}</p>

    <p>
        Please attach the covering letter with the Bank Guarantee and paste the envelope cutout on the courier.
        (Attached below)
    </p>

    <p>“Ensuring timely operations is the only source of growth”</p>

    <div>
        <b>Regards,</b><br>
        {{ $data['assignee'] }},<br>
        Approved by Team Leader
    </div>

</div>
