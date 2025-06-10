<div>
    <p>Dear Sir,</p>
    <p>
        The Result for Tender No. {{ $data['tender_no'] }}, Tender Name {{ $data['tender_name'] }} has been declared.
    </p>
    <p>
        The Result of the tender is {{ $data['result'] }}<br>
        L1 Price: {{ format_inr($data['l1_price']) }}<br>
        L2 Price: {{ format_inr($data['l2_price']) }}<br>
        Our Price: {{ format_inr($data['our_price']) }}
    </p>
    <p>Attached Below:</p>
    <ul>
        <li>
            Screenshot of Qualified Parties:
            @if ($data['qualified_parties_screenshot'])
                Attached
            @else
                Not Available
            @endif
        </li>
        <li>
            Screenshot of the Final result:
            @if ($data['final_result_screenshot'])
                Attached
            @else
                Not Available
            @endif
        </li>
    </ul>
    <p>
        @if ($data['result'] == 'won')
            Congratulations on the win.
        @else
            Best of luck for the next Tender.
        @endif
    </p>
    <p>Regards,<br>Team Leader<br>Volks Energie Pvt. Ltd.</p>
</div>
