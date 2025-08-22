<div style="padding: 0 50px;">
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
    <table class="no-border full-width border-collapse">
        <tbody class="no-border border-collapse">
            <tr>
                <td class="no-border" style="text-align: left;">
                    To,<br>
                    The Manager<br>
                    YES Bank Limited<br>
                    Nehru Place, New Delhi-110019.
                </td>
                <td class="no-border" style="text-align: right;">
                    Date: {{ date('d-m-Y') }}
                </td>
            </tr>
        </tbody>
    </table>

    <p>Subject - Authorization Letter for receiving of Bank Guarantee from Yes Bank.</p>

    <p>Dear Sir,</p>
    <p>
        We are maintaining Current/CC account no.
        {{ $data['bg_bank'] == 1 ? '003063300010771' : ($data['bg_bank'] == 2 ? '50200072650598' : ($data['bg_bank'] == 3 ? '920020003522297' : ($data['bg_bank'] == 4 ? '000405552416' : '003063300010771'))) }}
        in the name of M/s Volks Energie Private Limited. We
        hereby authorize the following persons to receive the following bank guarantee, from you on our behalf.
    </p>

    <table class="bordered full-width border-collapse">
        <tr class="bordered">
            <th class="bordered">SI No.</th>
            <th class="bordered">Party Name</th>
            <th class="bordered">BG amount</th>
        </tr>
        <tr class="bordered">
            <td class="bordered">1</td>
            <td class="bordered">{{ $data['bg_favour'] ?? 'Volks Energie Private Limited' }}</td>
            <td class="bordered">Rs. {{ format_inr($data['bg_amt']) }}</td>
        </tr>
    </table>

    <p>Name Of Authorized Person: __________________________ (Aadhaar No.:___________________).</p>

    <p>
        Kindly handover the above-mentioned documents pertaining to our company to the above-mentioned
        employees/representatives at our risk and responsibility.
    </p>

    <p>
        Thanking You, <br>
        For Volks Energie Private Limited
    </p>
    <br>
    <br>
    <p>
        Authorized Signatory<br>
        Encl: Self attested copy of Valid ID cards with photo as mentioned above:
    </p>
</div>
