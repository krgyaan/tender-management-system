<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;

        }

        .container {
            display: grid;
            border: 1px solid black;
            grid-template-columns: 260px 770px;
            height: 525px;
            width: 1031px;
            margin-inline: auto;
            border-bottom: 8px solid #0069AA;
            z-index: +1;
            box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
        }

        .sidebar {
            border-right: 1px dashed black;
            z-index: -1;

        }

        .main {
            display: grid;
            grid-template-columns: 180px 570px;
            grid-template-rows: 150px 370px;
            padding-left: 20px;
        }

        .main-top {
            grid-column: span 2;
            position: relative;
            min-width: 0;
        }

        .main-top-content {
            display: flex;
        }

        .main-top-info {
            font-size: 8px;
        }

        .main-top-right {
            margin-top: 20px;
            font-size: 10px;
        }

        .logo {
            position: absolute;
            top: 10px;
            right: 0px;
            z-index: -1;
        }

        .main-left {
            grid-row: 2/ 3;
        }

        .main-right {
            grid-column: 2/3;
            grid-row: 2/3;
            margin-right: 30px;
        }

        h1 {
            font-size: 16px;
            font-weight: 600;
        }

        .form-field {
            border-bottom: 1px solid;
        }

        .form-label {
            vertical-align: middle;
        }

        .form-input {
            display: flex;
            gap: 0;
            margin-inline: 8px;
        }

        .form-input-child {
            width: 14px;
            height: 14px;
            border: 1px solid black;
            padding: 0;
        }

        .dd-number {
            width: 120px;
            height: 30px;
            border: 1px solid black;
            text-align: center;
            margin-bottom: 4px;
            padding: 2px;
        }

        input {
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

        .denomination-table th,
        .denomination-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: 100;
        }

        .main-right-table {
            border-collapse: collapse;
            width: 100%;

        }

        .main-right-table th,
        .main-right-table td {
            border: 1px solid #000;
            padding: 5px;
            font-weight: 100;


        }

        .main-right th {
            height: 40px;
            padding: 0;

        }

        .main-right th span {
            position: absolute;
            top: 0;
            left: 0;



        }

        .main-right-table td:nth-child(1),
        .main-right-table th:nth-child(1) {
            width: 340px;
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
            width: 35px;
        }

        .receipt-table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        .receipt-table th,
        .receipt-table td {
            border: 1px solid #000;
            padding: 5px;
            font-weight: 100;
        }

        .receipt-table th:nth-child(1),
        .receipt-table td:nth-child(1) {
            width: 63px;
        }

        .receipt-table th:nth-child(2),
        .receipt-table td:nth-child(2) {
            width: 63px;
        }

        .receipt-table th:nth-child(3),
        .receipt-table td:nth-child(3) {
            width: 20px;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="sidebar">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/yes-bank-cust-logo.png'))) }}"
                alt="yes-bank-cust-logo.png" width="230px" style="margin-top: 8px">

            <div style="display: flex; position: relative;">
                <!-- Rotated Text -->
                <div
                    style="transform: rotate(-90deg); transform-origin: left top; white-space: nowrap;  position: absolute; left: 24px; top: 380px">
                    Customer copy (To be produced by customer while collecting the DD)
                </div>

                <!-- Normal Box -->
                <div style="width: 240px; height: 100%; padding-left: 45px;">
                    <div>DD APPLICATION FORM</div>

                    <div style="display: flex">
                        <div class="form-field" style="width:240px; padding-top: 8px;"></div>
                        <div style=" padding-inline: 8px;">BRANCH</div>
                    </div>

                    <div class="date" style="display: flex; margin-left: 54px; padding: 4px 4px; ">
                        <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>/
                        <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>/20
                        <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>
                    </div>

                    <div style="display: flex; padding: 8px 4px;">
                        <div>NAME OF APPLICANT -</div>
                        <div id="name" class="form-field" style="width: 100%; "></div>
                    </div>
                    <div id="name" class="form-field" style="width: 100%; padding: 8px 4px;"></div>

                    <div style="display: flex; padding: 8px 4px;">
                        <div>BENEFICIARY NAME -</div>
                        <div id="name" class="form-field" style="width: 100%;  "></div>
                    </div>
                    <div id="name" class="form-field" style="width: 100%; padding: 8px 4px;"></div>

                    <div style="display: flex; padding: 8px 4px;">
                        <div>PAYABLE AT -</div>
                        <div id="name" class="form-field" style="width: 100%;  "></div>
                    </div>

                    <div style="display: flex; padding: 8px 4px;">
                        <div>AMOUNT (IN WORDS) - </div>
                        <div id="name" class="form-field" style="width: 100%; "></div>
                    </div>
                    <div id="name" class="form-field" style="width: 100%; padding: 8px 4px;"></div>

                    <table style="border: 1px solid black;" class="receipt-table">
                        <tr>
                            <th> </th>
                            <th>Rs.</th>
                            <th>P.</th>
                        </tr>
                        <tr>
                            <td>AMOUNT</td>
                            <td></td>
                            <td></td>
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

                    <div class="main-top-info" style="padding: 8px 0 0 8px;">* Charges as per schedule of charges will
                        be applicable</div>

                    <div class="main-top-info" style="padding:20px 0 0 48px;">Bank Seal</div>

                </div>
            </div>
        </div>

        <div class="main">

            <div class="main-top">
                <h1 style="text-align: center; padding-bottom: 12px;">DEMAND DRAFT APPLICATION FORM</h1>
                <div>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/yes-bank-logo.png'))) }}"
                        width="220px" alt="yes-bank-logo.png" class="logo">
                </div>

                <div class="main-top-content">

                    <div class="main-top-left">
                        <div style="display : flex; gap: 20px; align-items: baseline;">
                            <div style="display: flex; align-items: baseline;">
                                <div id="branch" class="form-field" style="width: 120px"></div>
                                <div class= "form-label"><span>BRANCH</span></div>
                            </div>
                            <div style="display: flex; align-items: baseline; ">
                                <div class= "form-label" style="display: inline;">PAN NO. </div>
                                <div id="pan-no" class="form-input">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="main-top-info">1)Please issue a demand draft against:</span>
                            <div style="margin-top: 4px;">
                                <input class="form-input-child"><span class="main-top-info"> Cash (Amount limited upto
                                    Rs 49,999 including charges)</span>
                            </div>
                            <div style="display: flex; align-items: baseline; margin-top: 4px;" class="main-top-info">
                                <input class="form-input-child">
                                <div class="main-top-info">&nbsp;Debit my A/c no.</div>
                                <div id="acc-no" class="form-input">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">
                                    <input class="form-input-child">

                                </div>
                                <div>towards DD amount & applicable charges enclosing cheque no.</div>
                                <div id="branch" class="form-field" style="width: 40px; margin-inline: 4px;">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="main-top-info" style="margin-left: 16px;">(Cheque to be submitted for DD
                                amount)</div>
                        </div>
                    </div>

                    <div class="main-top-right">
                        <div class="dd-number ">DD Number</div>
                        <div class="date" style="display: flex;">
                            <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>/
                            <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>/20
                            <div id="day" class="form-field" style="width: 28px; margin-inline: 4px;"></div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="main-left">
                <table class="denomination-table">
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
                            BENEFICIARY NAME
                        </th>
                        <th style="vertical-align: bottom; padding: 4px 6px">
                            DD AMOUNT
                        </th>
                        <th style="vertical-align: top; text-align: left; ">
                            <div style="padding: 4px 6px">Rs.</div>
                            <div style="border-top: 1px solid black ;"></div>
                        </th>
                        <th style="vertical-align: top;">
                            <div style="padding: 4px 6px">P.</div>
                            <div style="border-top: 1px solid black ;"></div>
                        </th>
                    </tr>
                    <tr>
                        <td style="position: relative; ">
                            <div style="width:340px; overflow-wrap:normal">
                                <span style="position: absolute; top: 4px; left: 6px;">AMOUNT (IN WORDS)</span>
                                <div class="form-field" style="width: 200px; margin-left:125px; "></div>

                            </div>
                        </td>
                        <td>DD CHARGES*</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>PAYABLE AT</td>
                        <td>TOTAL</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div style="border: 1px solid black; width: 100%; min-height: 40px; border-top: 0; padding: 4px 6px;">
                    Please deliver the DD to the bearer Mr./Ms
                    <div class="form-field" style="display: inline-block; width: 240px"></div>
                    whose appended signature (s) is duly attested below
                </div>

                <div style="display: flex; height: 80px; border: 1px black solid; border-top: 0;">
                    <div style="border-right: 1px solid black; width: 240px;">
                        <div style="border-bottom: 1px solid black; height: 40px; padding: 4px 4px;">BEARER SIGNATURE
                        </div>
                        <div style="height: 40px; padding: 4px 4px;">SIGNATURE OF APPLICANT</div>
                    </div>
                    <div style="width: 300px">
                        <div style="padding: 4px 4px; border-bottom: 1px solid black;">
                            <div style="width: 160px;">NAME & ADDRESS OF APPLICANT: </div>
                        </div>
                        <div style="padding: 4px 4px; border-bottom: 1px solid black;">MOBILE / TELEPHONE:</div>
                        <div style="padding: 4px 4px;">Email id:</div>
                    </div>
                </div>

                <div>

                    <div style="text-align: center; padding: 4px; font-weight:600;">
                        <span>FOR BRANCH USE ONLY</span>
                    </div>
                    <div style="display: flex; padding: 2px 4px;">
                        <div style=" align-items: baseline;">Acknowledgement from the recipient after delivery of the
                            DD </div>
                        <div style="margin-left: 24px; padding-inline: 12px; padding-top: 8px;">
                            <div class="form-field" id="signature" style="width: 160px"></div>
                            <div style="vertical-align: bottom; text-align: center; padding-top: 4px;">Name & Signature
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; margin-top: 30px; width: 100%; gap: 36px">
                    <div style=" padding-inline: 12px; padding-top: 8px;">
                        <div class="form-field" id="processed-by" style="width: 120px"></div>
                        <div style="vertical-align: bottom; text-align: center; padding-top: 4px;">Processed By.</div>
                    </div>

                    <div style=" padding-inline: 12px; padding-top: 8px;">
                        <div class="form-field" id="authorised-by" style="width: 120px"></div>
                        <div style="vertical-align: bottom;  text-align: center; padding-top: 4px;">Authorised By</div>
                    </div>

                    <div style=" padding-inline: 12px; padding-top: 8px;">
                        <div class="form-field" id="ref-no" style="width: 120px"></div>
                        <div style="vertical-align: bottom;  text-align: center; padding-top: 4px;">Ref No</div>
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
