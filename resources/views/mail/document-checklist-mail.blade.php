<div>
    <p>Dear {{ $data['tl'] }},</p>

    <p>Following are the documents required for Tender No. {{ $data['tenderNo'] }}</p>

    <h4>Tender Details:</h4>
    <ul>
        <li>Tender Name: {{ $data['tenderName'] }}</li>
        <li>Tender Number: {{ $data['tenderNo'] }}</li>
    </ul>

    <p>Checked Documents</h4>
    <ul>
        @foreach ($data['documents'] as $doc)
            <li>{{ $doc }}</li>
        @endforeach
    </ul>
    <p>Regards,<br>{{ $data['te'] }}</p>
</div>
