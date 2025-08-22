<div>
    <p>Dear Sir,</p>

    <p>We request the following modifications to the existing Bank Guarantee as detailed below:</p>

    <ul>
        <li>Bank Guarantee No.: {{ $data['bg_no'] }}</li>
        <li>Bank Guarantee Date: {{ date('d-m-Y', strtotime($data['bg_date'])) }}</li>
        <li>Bank Guarantee in favor of: {{ $data['bg_favour'] }}</li>
        <li>Bank Guarantee Amount: Rs. {{ format_inr($data['bg_amt']) }}</li>
    </ul>

    <p>The request letter/email confirmation from the client has been attached to this email. Please debit our account
        No. 003063300010771, in case there are any additional charges to be paid for this modification.</p>

    <p>Please find attached the following documents:</p>
    <ul>
        <li>Request Letter on VE Letterhead</li>
        <li>Request letter/Email from the client requesting the Extension/Modification of the Bank Guarantee.</li>
        <li>Soft Copy of the Bank Guarantee.</li>
        <li>Soft Copy of the FDR</li>
    </ul>

    <p>The hard copy of the Bank Guarantee and request letter on our letterhead has been sent physically to the Bank
        branch. Please contact the undersigned for any further clarification.</p>

    <p>Best Regards,</p>
    <p>Piyush Goyal<br>Director<br>Volks Energie Pvt. Ltd.</p>
    <p>Imran Khan,<br>+91-88825-91733<br>Accounts Leader,<br>Volks Energie Pvt. Ltd.</p>
</div>
