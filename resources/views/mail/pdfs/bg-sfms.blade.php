<div style="padding: 0 50px;">
    <style>
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

        .padding-8 {
            padding: 8px;
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


    <table class="no-border full-width">
        <tbody class="no-border">
            <tr>
                <td class="no-border" style="text-align: left;">
                    To,<br>
                    {{ $data['bg_favour'] }}<br>
                    {!! nl2br(wordwrap($data['bg_address'], 40, "\n")) !!}
                </td>
                <td class="no-border" style="text-align: right;">
                    Date: {{ date('d-m-Y') }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>

    <p>
        Tender No: {{ $data['tender_no'] }}<br>
        Subject: Confirmation Regarding Submitted Bank Guarantee via SFMS against {{ $data['tender_name'] }}
    </p>

    <p>Dear Sir/Madam,</p>
    <p>With reference to Tender No: {{ $data['tender_no'] }} dated {{ date('d-m-Y', strtotime($data['due_date'])) }} for
        {{ $data['tender_name'] }},</p>
    <p>
        The Bank Guarantee (BG) submitted against
        {{ $data['bg_purpose'] == 'advance' ? 'Advance Payment' : ($data['bg_purpose'] == 'deposit' ? 'Security Bond/ Deposit' : ($data['bg_purpose'] == 'bid' ? 'Bid Bond' : ($data['bg_purpose'] == 'performance' ? 'Performance' : ($data['bg_purpose'] == 'financial' ? 'Financial' : ($data['bg_purpose'] == 'counter' ? 'Counter Guarantee' : '______________________'))))) }},
        we would like to bring to your attention that the Structured Financial Messaging System (SFMS) serves as the
        official confirmation of the Bank Guarantee (BG) issued to you. Since SFMS is directly issued by the bank, it
        already acts as a verification mechanism supported by the Reserve Bank of India (RBI), ensuring the authenticity
        and validity of the BG.
    </p>
    <p>
        Therefore, an additional bank confirmation letter is not required. However, if you still require a separate
        confirmation letter from the bank, you may contact or reach out to the following mentioned Yes Bank
        representatives further:
    </p>
    <p>Yes Bank Ltd - Nehru Place, New Delhi</p>
    <ol>
        <li>Dhiraj.kumar3@yesbank.in</li>
        <li>Divya.khurana1@yesbank.in</li>
        <li>dtcorpdesknehruplace@yesbank.in</li>
        <li>yesbgconfirmation@yesbank.in</li>
    </ol>
    <p>Phone No: +91-9643467407 (Divya Khurana(CSD))</p>
    <p>Please let us know if you need any further clarification.</p>

    <br>
    Thanks & Regards,<br>
    Piyush Goyal (Director)<br>
    Volks Energie Pvt Ltd<br>
    B-1/D-8, 2nd floor, Mohan Cooperative<br>
    Industrial Estate, New Delhi -110044
</div>
