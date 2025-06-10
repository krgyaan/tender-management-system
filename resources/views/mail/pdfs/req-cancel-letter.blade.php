<div>
    <style>
        * {
            font-family: 'Calibri Light', sans-serif;
        }

        .no-border {
            border: none;
        }

        .full-width {
            width: 100%;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .bordered {
            border: 1px solid;
        }

        .text-left {
            text-align: left;
        }

        .padding-4 {
            padding: 4px;
        }
    </style>
    <div style="border-top: 10px solid #333; border-bottom: 5px solid #f04e23;">
    </div>
    <div style="padding: 10px 0; font-family: sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tr>
                <!-- Left: Logo -->
                <td style="width: 50%; padding-left: 20px;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/logo.png'))) }}"
                        alt="Volks Energie Logo" style="height: 60px;">
                </td>

                <!-- Right: Contact Info -->
                <td style="width: 50%; text-align: right; font-size: 10px; color: #333; padding-right: 20px;">
                    <div>
                        B-1/D8, 2nd floor,<br>
                        Mohan Cooperative Industrial Estate,<br>
                        New Delhi 110044
                    </div>
                    <div>
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/phone.png'))) }}"
                            alt="Phone" style="height: 12px; vertical-align: middle;">
                        +91 9650393636, +91 9654551781<br>
                        Accounts: +91 8882591733
                    </div>
                    <div>
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/email.png'))) }}"
                            alt="Email" style="height: 12px; vertical-align: middle;">
                        contact@volksenergie.in
                    </div>
                    <div>
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('pdfimages/web.png'))) }}"
                            alt="Website" style="height: 12px; vertical-align: middle;">
                        www.volksenergie.in
                    </div>
                </td>
            </tr>
        </table>
        <hr />
    </div>

    To,<br>
    The Branch Manager,<br>
    @if ($data['bg_bank'] == 'YESBANK_2011' || $data['bg_bank'] == 'YESBANK_0771')
        Yes Bank Limited
    @elseif ($data['bg_bank'] == 'PNB_6011')
        Punjab National Bank
    @elseif ($data['bg_bank'] == 'SBI')
        State Bank of India
    @elseif ($data['bg_bank'] == 'HDFC_0026')
        HDFC Bank Limited
    @elseif ($data['bg_bank'] == 'ICICI')
        ICICI Bank Limited
    @endif
    <br>
    Nehru Place, New Delhi- 110019.<br>

    <p>Sub: Request to release Bank Guarantee & Lien FDR.</p>
    <p>
        We confirm that the following Bank Guarantee has served its purpose. We hereby request you to discharge the Bank
        Guarantee issued in favour of {{ $data['bg_favour'] }} without any claim against the same. We kindly request you
        to release/close the below-mentioned Bank Guarantee, including its backed FDR and credit the amount to our bank
        account No. 003063300010771, as per details below.
    </p>
    <ul>
        <li>Bank Guarantee No.: {{ $data['bg_no'] }}</li>
        <li>Bank Guarantee Date: {{ date('d-m-Y', strtotime($data['bg_date'])) }}</li>
        <li>Bank Guarantee in favor of: {{ $data['bg_favour'] }}</li>
        <li>Bank Guarantee Value: Rs. {{ format_inr($data['bg_amt']) }}</li>
        <li>FDR No.: {{ $data['fdr_no'] }}</li>
        <li>FDR Value: Rs. {{ format_inr($data['fdr_amt']) }}</li>
    </ul>
    <p>Please find attached the following documents:</p>
    <ul>
        <li>Covering letter from the client confirming the cancellation of the Bank Guarantee.</li>
        <li>Soft Copy of the Bank Guarantee.</li>
        <li>Soft Copy of the FDR</li>
    </ul>

    <p>
        We appreciate your immediate action on this matter. Please feel free to contact the undersigned for further
        clarification, if needed.
    </p>

    <div style="margin-top: 20px;">
        <p>Best Regards,</p>
        <p>Piyush Goyal<br>Director<br>Volks Energie Pvt. Ltd.</p>
        <p>Imran Khan,<br>+91-88825-91733<br>Accounts Leader,<br>Volks Energie Pvt. Ltd.</p>
    </div>
</div>
