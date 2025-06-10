<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DD Cancellation Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            margin-bottom: 20px;
        }
        .subject {
            font-weight: bold;
            margin: 20px 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>
            To,<br>
            Yes Bank<br>
            The Branch Manager<br>
            Mohan Cooperative Industrial Estate<br>
            New Delhi, 110044
        </p>
        
        <p>Date: {{ date('jS M Y') }}</p>
    </div>
    
    <div class="subject">
        Subject: Request for DD Cancellation
    </div>
    
    <div class="content">
        <p>Dear Sir,</p>
        
        <p>You are hereby requested to withdraw the mentioned Demand Draft with no. {{ $data['dd_no'] ?? '' }}, which was issued by your bank on {{ $data['dd_date'] ?? '' }} for Rs. {{ $data['amount'] ?? '' }}, respectively, in favour of {{ $data['beneficiary_name'] ?? '' }}. The DD has not been used as the tender was lost.</p>
        
        <p>
            Please cancel and credit the amount to our:<br>
            Account No.: 003084600002011<br>
            IFSC Code: YESB0000030
        </p>
        
        <p>If any charges are incurred, please debit our account.</p>
        
        <p>Thank you.</p>
    </div>
    
    <div class="signature">
        <p>
            Best Regards,<br>
            Shivani Sejwar<br>
            (Account Executive - Sale)<br>
            Volks Energie Pvt Ltd
        </p>
    </div>
</body>
</html>
