<div>
    <p>Dear {{ $data['te_name'] }},</p>

    <p>The request for Demand Draft (DD) creation has been {{ $data['status'] }}</p>

    <ul>
        <li>DD Date: {{ $data['dd_date'] }}</li>
        <li>DD No.: {{ $data['dd_no'] }}</li>
        <li>Beneficiary Name: {{ $data['beneficiary_name'] }}</li>
        <li>Payable at: {{ $data['payable_at'] }}</li>
        <li>Amount: Rs. {{ $data['amount'] }}</li>
        <li>Soft copy of the DD: attached below</li>
        <li>Docket No. of the courier: {{ $data['docket_no'] }}</li>
        <li>Soft copy of the Docket slip: attached below</li>
    </ul>
    
    @if($data['status'] == 'Accepted' && isset($data['remarks']))
        <p>Remarks: {{ $data['remarks'] }}</p>
    @endif

    <p>Best of luck for the tender.</p>

    <p>
        <b>Regards,</b><br>
        Shivani,<br>
        Accounts team.
    </p>
</div>
