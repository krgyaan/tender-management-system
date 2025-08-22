<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    *{
      box-sizing: border-box;
    }

    body{
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;

    }
    
    .container{
      position: relative;
      margin-top: 12px;
      border: 1px solid black;
      height: 525px;
      width: 1031px;
      margin: 0 auto;
      border-bottom: 8px solid #0069AA;
      z-index: +1;
      box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
    }

    .sidebar{
      position: absolute;
      left: 0;
      top: 0;
      width: 260px;
      height: 100%;
      border-right: 1px dashed black;
      z-index: -1;
    }

    .main{
      position: absolute;
      left: 260px;
      top: 0;
      width: 770px;
      height: 100%;
      padding-left: 20px;
    }

    .main-top{
      position: relative;
      height: 150px;
      width: 100%;
      min-width: 0;
    }

    .main-top-content{
      position: relative;
      width: 100%;
    }

    .main-top-info{
      font-size: 10px;
    }
    
    .main-top-right{
      position: absolute;
      right: 0;
      top: 20px;
      font-size: 10px;
    }

    .logo{
      position: absolute;
      top: 10px;
      right: 0px;
      z-index: -1;
    }

    .logo2 {
      position: absolute;
      top: 10px;
      right: 18px;
      z-index: -1;
    }

    .main-left{
      position: absolute;
      top: 140px;
      left: 0;
      width: 180px;
      height: 370px;
    }

    .main-right{
      position: absolute;
      top: 140px;
      left: 200px;
      width: 570px;
      height: 370px;
      max-width: 530px;

    }

    h1{
      font-size: 16px;
      font-weight: 600;
      text-align: center;
      padding-bottom: 12px;
      margin: 0;
    }

    .form-field{
      border-bottom: 1px solid;
    }
    .form-label{
      vertical-align: middle;
    }
    .form-input{
      display: inline-block;
      margin-inline:8px;
    }
    .form-input-child{
      width:12px;
      height:12px;
      border: 1px solid black;
      padding:0;
      margin: -1px;
    }

    .dd-number{
      width: 120px;
      height: 30px;
      border: 1px solid black;
      text-align: center;
      margin-bottom: 4px;
      padding: 2px;
    }
    input{
      margin: 0;
    }

    .denomination-table {
      width: 100%;
      margin-bottom: 15px;
      border-collapse: collapse;
    }
    
   .denomination-table td:nth-child(2),
   .denomination-table th:nth-child(2) {
      width: 70px;
    }
   .denomination-table td:nth-child(3),
   .denomination-table th:nth-child(3) {
      width: 24px;
    }

    .denomination-table th, .denomination-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
      font-weight: 100;
    }

    .main-right-table{
      border-collapse: collapse;
      width: 500px;      
    }

    .main-right-table th, .main-right-table td {
      border: 1px solid #000;
      padding: 5px;
      font-weight: 100;
    }

    .main-right th{
      height: 40px;
      padding: 0;
    }

    .main-right th span{
      position: absolute;
      top: 0;
      left: 0;
    }

    .main-right-table td:nth-child(1),
    .main-right-table th:nth-child(1) {
      width: 320px;
    }

    .main-right-table th:nth-child(2),
    .main-right-table td:nth-child(2) {
      width: 80px;
    }

    .main-right-table th:nth-child(3),
    .main-right-table td:nth-child(3) {
      width: 70px;
    }

    .main-right-table th:nth-child(4),
    .main-right-table td:nth-child(4) {
      width: 25px;
    }

    .receipt-table{
      border: 1px solid black;
      border-collapse: collapse;
      width: 200px;
    }

    .receipt-table th, .receipt-table td {
      border: 1px solid #000;
      padding: 5px;
      font-weight: 100;
    }

    .receipt-table th:nth-child(1),
    .receipt-table td:nth-child(1){
      width:50px;
    }

    .receipt-table th:nth-child(2),
    .receipt-table td:nth-child(2){
      width:50px;
    }

    .receipt-table th:nth-child(3),
    .receipt-table td:nth-child(3){
      width:20px;
    }
    
    /* Table-based layout styles */
    .table-container {
      display: table;
      width: 100%;
      height: 100%;
    }
    
    .table-row {
      display: table-row;
    }
    
    .table-cell {
      display: table-cell;
      vertical-align: top;
    }
    
    .main-top-left {
      width: 550px;
    }
    
    .rotated-text {
      transform: rotate(-90deg);
      transform-origin: left top;
      white-space: nowrap;
      position: absolute;
      left: 24px;
      top: 380px;
    }
    
    .signature-box {
      width: 160px;
      border-bottom: 1px solid;
      display: inline-block;
    }
    
    .signature-label {
      text-align: center;
      padding-top: 4px;
    }
    .form-input input{
      vertical-align: baseline;
    }

    .table{
      padding: 0;
      margin: 0;
      border-spacing: 0;
    }
    .form-area{
    word-break: break-all;/* Allows text to wrap */

    }
  </style>
</head>
<body>

  <div class="container">
    
    <div class="sidebar">
      <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/yes-bank-cust-logo.png'))) }}" alt="yes-bank-cust-logo.png" width="230px" style="margin-top: 8px">

      <div style="position: relative; height: 100%;">
        <!-- Rotated Text -->
        <div class="rotated-text">
          Customer copy (To be produced by customer while collecting the DD)
        </div>

        <!-- Normal Box -->
        <div style="width: 240px; height: 100%; padding-left: 45px; position: absolute; top: 0; left: 0;">
         <div>DD APPLICATION FORM</div>
          <div class="form-area" style="margin-block: 8px;">
            <span>___________________</span>BRANCH
          </div>

          
         <div class="date" style="display: table; margin-left: 54px; padding: 4px 4px; margin-bottom: 8px;">
            <span class="form-area">_____</span>
            <div style="display: table-cell;">/</div>
            <span class="form-area">_____</span>
            <div style="display: table-cell;">/20</div>
            <span class="form-area">_____</span>
          </div>
          
          <div class="form-area" style="margin-block: 20px; padding-top: 10px;">
            NAME OF APPLICANT - <br> VOLKS ENERGIE PRIVATE LIMITED
          </div>
          
          
          <div class="form-area" style="margin-block: 20px; padding-top: 10px;">
            BENEFICIARY NAME - {{$data['beneficiary_name']}}
          </div>
          

          <div class="form-area" style="margin-block: 20px; padding-top: 10px;">
            PAYABLE AT - {{$data['payable_at']}}
          </div>
          

          <div class="form-area" style="margin-block: 20px; padding-top: 10px;">
            AMOUNT (IN WORDS) - {{$data['dd_amount_in_words']}}
          </div>
          
          <table style="border: 1px solid black; margin-top: 30px;" class="receipt-table">
            <tr>
              <th> </th>
              <th>Rs.</th>
              <th>P.</th>
            </tr>
            <tr>
              <td>AMOUNT</td>
              <td>{{$data['dd_amount']}}/-</td>
              <td>00</td>
            </tr>
            <tr>
              <td>DD CHARGES*</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>TOTAL</td>
              <td></td>
              <td></td>
            </tr>
          </table>

          <div class="main-top-info" style="padding: 8px 0 0 8px;">* Charges as per schedule of charges will be applicable</div>

          <div class="main-top-info" style="padding:20px 0 0 48px;">Bank Seal</div>

        </div>
      </div>
    </div>
    
    <div class="main">
      <div class="main-top">
        <h1 style="padding-top:12px ;">DEMAND DRAFT APPLICATION FORM</h1>
        <div><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/yes-bank-logo.png'))) }}" width="200px" alt="yes-bank-logo.png" class="logo2"></div>
        
        <div class="main-top-content">
          <table style="width: 100%;">
            <tr>
              <td style="width: 600px; vertical-align: top;">
                <div style="display: table; width: 100%;">
                  <div class="form-area" style="margin-block: 8px;">
                    <span>___________________</span>BRANCH
                  </div>
                  <div style="display: table-cell; "></div>
                  <div style="display: table-cell;">
                    <span class="form-label">PAN NO.</span>
                  </div>
                  <div>
                    <div id="pan-no" class="form-input">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="A">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="A">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="D">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="C">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="V">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="9">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="3">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="9">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="6">
                      <input class="form-input-child" style="margin: -2px; padding: 1px;" value="C">
                    </div>
                  </div>
                </div>
                
                <div style="position: relative">
                  <span class="main-top-info">1)Please issue a demand draft against:</span>
                  <div style="margin-top: 2px;">
                    <input class="form-input-child"><span class="main-top-info"> Cash (Amount limited upto Rs 49,999 including charges)</span>
                  </div>
                  <div style="margin-top: 2px;">
                      <!--<input class="form-input-child" value="">-->
                      @php
                        $tick = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('pdfimages/tick.png')));
                      @endphp
                      <img src="{{ $tick }}" style="height: 13.5px;" class="form-input-child">
                      <span class="main-top-info"> Debit my A/c no.</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">3</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">8</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">4</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">6</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">2</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">0</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">1</span>
                      <span style="border: 1px solid #000; margin: -2px; padding: 1px;">1</span>
                      <span class="main-top-info">towards DD amount & applicable charges enclosing cheque no. {{$data['cheque']}}</span>
                  </div>
                </div>
                <div>
                  <div class="main-top-info" style="margin-left: 16px;">(Cheque to be submitted for DD amount)</div>
                </div>
              </td>
              <td style="vertical-align: top;">
                <div class="main-top-right" style="position: relative;">
                  <div class="dd-number" style="position: absolute; right: 36px;">DD Number</div>
                  <div class="date" style="display: table; position: absolute; right: 36px; top: 40px">
                      <span class="form-area">_____</span>
                      <div style="display: table-cell;">/</div>
                      <span class="form-area">_____</span>
                      <div style="display: table-cell;">/20</div>
                      <span class="form-area">_____</span>
                  </div>              
                </div>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="main-left" style="position: relative;">
          <table class="denomination-table" style="position: absolute; left: 0px; top: -150px">
            <tr>
                <th></th>
                <th>Rs.</th>
                <th>P.</th>
            </tr>
            <tr>
                <td>CASH</td>
                <td id="cash-rs"></td>
                <td id="cash-p"></td>
            </tr>
            <tr>
                <td>X 2000</td>
                <td id="2000-rs"></td>
                <td id="2000-p"></td>
            </tr>
            <tr>
                <td>X 500</td>
                <td id="500-rs"></td>
                <td id="500-p"></td>
            </tr>
            <tr>
                <td>X 200</td>
                <td id="200-rs"></td>
                <td id="200-p"></td>
            </tr>
            <tr>
                <td>X 100</td>
                <td id="100-rs"></td>
                <td id="100-p"></td>
            </tr>
            <tr>
                <td>X 50</td>
                <td id="50-rs"></td>
                <td id="50-p"></td>
            </tr>
            <tr>
                <td>X 20</td>
                <td id="20-rs"></td>
                <td id="20-p"></td>
            </tr>
            <tr>
                <td>X 10</td>
                <td id="10-rs"></td>
                <td id="10-p"></td>
            </tr>
            <tr>
                <td>X 5</td>
                <td id="5-rs"></td>
                <td id="5-p"></td>
            </tr>
            <tr>
                <td>X 2</td>
                <td id="2-rs"></td>
                <td id="2-p"></td>
            </tr>
            <tr>
                <td>X 1</td>
                <td id="1-rs"></td>
                <td id="1-p"></td>
            </tr>
            <tr>
                <td>COINS</td>
                <td id="coins-rs"></td>
                <td id="coins-p"></td>
            </tr>
            <tr>
                <td>TOTAL</td>
                <td id="denomination-total-rs"></td>
                <td id="denomination-total-p"></td>
            </tr>
          </table>
      </div>
      <div class="main-right">
        
        <table class="main-right-table">
          <tr>
            <th style="text-align: left; vertical-align: top; padding: 4px 6px">
              BENEFICIARY NAME &nbsp; {{$data['beneficiary_name']}}
            </th>
            <th style="vertical-align: bottom; padding: 4px 6px">
              DD AMOUNT 
            </th>
            <th style="vertical-align: top; text-align: left; ">
              <div style="padding: 4px 6px">Rs.</div>
              <div style="border-top: 1px solid black;">{{$data['dd_amount']}}/-</div>
            </th>
            <th style="vertical-align: top;">
              <div style="padding: 4px 6px">P.</div>
              <div style="border-top: 1px solid black ;">00</div>
            </th>
          </tr>
          <tr>
            <td style="position: relative; ">
              <div style="width: 100% overflow-wrap:normal">
                <span style="position: absolute; top: 4px; left: 6px;"></span>
                <div class="form-area">AMOUNT (IN WORDS) &nbsp;&nbsp; {{$data['dd_amount_in_words']}}</div>
              </div>
            </td>
            <td>DD CHARGES*</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td>PAYABLE AT &nbsp;&nbsp; {{$data['payable_at']}}</td>
            <td>TOTAL</td>
            <td></td>
            <td></td>
          </tr>
        </table>
        
        <div style="border: 1px solid black; width: 100%; min-height: 40px; border-top: 0; padding: 4px 6px;">
          <div class="form-area">Please deliver the DD to the bearer Mr./Ms <span>__________________________________________</span>
          </div>whose appended signature (s) is duly attested below
          
        </div>

        <div style="display: table; width: 100%; height: 80px; border: 1px black solid; border-top: 0;">
          <div style="display: table-cell; border-right: 1px solid black; width: 241px;">
            <div style="border-bottom: 1px solid black; height: 40px; padding: 3px 4px;">BEARER SIGNATURE</div>
            <div style="height: 40px; padding: 0 4px;">SIGNATURE OF APPLICANT</div>
          </div>
          <div style="display: table-cell; width: 300px">
            <div style="padding: 3px 4px; border-bottom: 1px solid black;">
              <div style="width: 100%;">NAME & ADDRESS OF APPLICANT: </div>
              <span>Kailash, C/o, Volks Energie Pvt. Ltd. New Delhi</span>
            </div>
            <div style="padding: 4px 4px; border-bottom: 1px solid black;">MOBILE / TELEPHONE: +91-8826682356</div>
            <div style="padding: 0 4px;">Email id: accounts@volksenergie.in</div>
          </div>
        </div>
        
        <div>
          <div style="text-align: center; padding: 4px; font-weight:600;">
            <span>FOR BRANCH USE ONLY</span>
          </div>
          <div style="display: table; width: 100%; padding: 0px;">
            <div style="display: table-cell; vertical-align: middle;">
              Acknowledgement from the recipient after delivery of the DD 
            </div>
            <div style="display: table-cell; padding-left: 24px; padding-inline: 12px;">
              <div class="form-area"><span>_________________</span></div>
              <div class="signature-label">Name & Signature</div>
            </div>
          </div>
        </div>

        <div style="display: table; width: 100%; padding-top: 5px;">
          <div style="display: table-cell; padding-inline: 12px;">
              <div class="form-area"><span>_____________________</span></div>
              <div class="signature-label">Processed By.</div>
          </div>
          
          <div style="display: table-cell; padding-inline: 12px;">
              <div class="form-area"><span>_____________________</span></div>
              <div class="signature-label">Authorised By</div>
          </div>

          <div style="display: table-cell; padding-inline: 12px;">
              <div class="form-area"><span>_____________________</span></div>
              <div class="signature-label">Ref No</div>
          </div>
        </div>

        <div class="main-top-info" style="padding: 12px 0px 0px 36px">
          * Charges as per schedule of charges will be applicable
        </div>
      </div>
    </div>
  </div>
  
</body>
</html>