<div>
    <p>Dear Sir,</p>
    <p>
        The RA for Tender No. {{ $data['tender_no'] }}, Tender Name {{ $data['tender_name'] }} has concluded.
    </p>
    <p>
        The Result of the RA is {{ $data['ra_result'] }}<br>
        Our Price was L1 at the start of the RA: {{ $data['ve_l1_start'] == 'yes' ? 'Yes' : 'No' }}<br>
        RA Start Price: {{ $data['ra_start_price'] }}<br>
        RA Close Price: {{ $data['ra_close_price'] }}<br>
        RA Duration: {{ $data['ra_duration'] }}
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
            Screenshot of all decrements:
            @if ($data['decrements_screenshot'])
                Attached
            @else
                Not Available
            @endif
        </li>
        <li>
            Screenshot of the Final result:
            @if ($data['final_result'])
                Attached
            @else
                Not Available
            @endif
        </li>
    </ul>
    <p>
        @if ($data['ra_result'] == 'won')
            Congratulations on the win.
        @elseif($data['ra_result'] == 'lost')
            We will try harder the next time.
        @elseif($data['ra_result'] == 'h1_elimination')
            We will talk to the OEM to get better prices.
        @endif
    </p>
    <p>Regards,<br>Team Leader<br>Volks Energie Pvt. Ltd.</p>
</div>
