<div style="font-family: Arial, sans-serif;">
    <p>Dear Yesbank team,</p>
    
    <p>Please find attached the request letter for the cancellation of the Demand Draft. The physical copy of the letter and DD has been sent to your branch.</p>
    
    <ul>
        <li>DD No.: {{ $data['dd_no'] ?? '' }}</li>
        <li>Beneficiary Name: {{ $data['beneficiary_name'] ?? '' }}</li>
        <li>Amount: Rs. {{ $data['amount'] ?? '' }}</li>
    </ul>
    
    <p>Please cancel and credit the amount to our account:</p>
    <ul>
        <li>Account No.: 003084600002011</li>
        <li>IFSC Code: YESB0000030</li>
    </ul>

    <p>
        Regards,<br>
        Piyush Goyal<br>
        Director<br>
        Volks Energie Pvt Ltd<br>
        B-1/D-8, 2nd floor, Mohan Cooperative<br>
        Industrial Estate, New Delhi -110044
    </p>
</div>
