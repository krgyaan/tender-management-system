<div>
    <p>Dear Accounts team,</p>
    <p>
        The following cheque's due date is about to be reached. Please stop the cheque to avoid bouncing the cheque.
    </p>
    <ul>
        <li>Cheque No.: {{ $data['chequeNo'] }}</li>
        <li>Party Name: {{ $data['partyName'] }}</li>
        <li>Amount: {{ format_inr($data['amount']) }}</li>
        <li>Due Date: {{ $data['dueDate'] }}</li>
        <li>Reason for Stopping cheque: {{ $data['reason'] }}</li>
    </ul>
    <p>“Timely payments ensure credibility in the market”</p>
    <p>Regards,<br>Admin</p>
</div>
