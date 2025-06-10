<div>
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
    <p>
        Authorized Signatory<br>
        Encl: Self attested copy of Valid ID cards with photo as mentioned above:
    </p>
</div>
