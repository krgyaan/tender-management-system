<div>
    <p>Dear Sir,</p>

    <p>
        We confirm that the following Bank Guarantee has served its purpose. We hereby request you to discharge the
        Bank Guarantee issued in favour of {{ $data['bg_favour'] }} without any claim against the same. We kindly
        request you to release/close the below-mentioned Bank Guarantee, including its backed FDR, and credit the amount
        to our bank account as per details below.
    </p>

    <ul>
        <li>Bank Guarantee No.: {{ $data['bg_no'] }}</li>
        <li>Bank Guarantee Date: {{ $data['bg_date'] ? date('d-m-Y', strtotime($data['bg_date'])) : '' }}</li>
        <li>Bank Guarantee in favor of: {{ $data['bg_favour'] }}</li>
        <li>Bank Guarantee Value: Rs. {{ format_inr($data['bg_amt']) }}</li>
        <li>FDR No.: {{ $data['fdr_no'] }}</li>
        <li>FDR Value: Rs. {{ format_inr($data['fdr_amt']) }}</li>
    </ul>

    <p>Please find attached the following documents:</p>
    <ul>
        <li>Request Letter on VE Letterhead</li>
        <li>Covering letter from the client confirming the cancellation of the Bank Guarantee.</li>
        <li>Soft Copy of the Bank Guarantee.</li>
        <li>Soft Copy of the FDR</li>
    </ul>

    <p>Please credit the amount in our bank account.</p>

    <p>
        The hard copy of the Bank Guarantee and covering letter on our letterhead has been sent physically to the Bank
        branch. Please contact the undersigned for any further clarification.
    </p>

    <p>Best Regards,</p>
    <p>Piyush Goyal<br>Director<br>Volks Energie Pvt. Ltd.</p>
    <p>Imran Khan,<br>+91-88825-91733<br>Accounts Leader,<br>Volks Energie Pvt. Ltd.</p>

</div>
