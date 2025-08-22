<div>
    <p>Dear {{ $data['org'] }} Team,</p>
    <div>
        <p>
            Please provide the best price for the requirements stated below. Also, please find the relevant
            techno-commercial documents attached for your reference.
        </p>

        @if ($data['items'])
            <p>Here are the requirements:</p>
            <table style="border: 1px solid black; border-collapse: collapse;">
                <thead style="background-color: #ddd;">
                    <tr>
                        <th style="border: 1px solid black; padding: 5px;">Requirement</th>
                        <th style="border: 1px solid black; padding: 5px;">Qty</th>
                        <th style="border: 1px solid black; padding: 5px;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['items'] as $item)
                    <tr>
                        <td style="border: 1px solid black; padding: 5px;">{{ $item['requirement'] }}</td>
                        <td style="border: 1px solid black; padding: 5px;">{{ $item['qty'] }}</td>
                        <td style="border: 1px solid black; padding: 5px;">{{ $item['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p>Here are the documents:</p>
        <ul>
            <li>Scope of Work - {{ in_array('scope', $data['keys']) ? 'Attached' : 'NA' }}</li>
            <li>Technical Specifications - {{ in_array('technical', $data['keys']) ? 'Attached' : 'NA' }}</li>
            <li>Detailed BOQ - {{ in_array('boq', $data['keys']) ? 'Attached' : 'NA' }}</li>
            <li>MAF format - {{ in_array('maf', $data['keys']) ? 'Attached' : 'NA' }}</li>
            <li>MII Format - {{ in_array('mii', $data['keys']) ? 'Attached' : 'NA' }}</li>
            <li>List of Docs needed from manufacturer: {{ $data['list_of_docs'] }}</li>
        </ul>
        <br>
        <b>Please send the Techno-Commercial Offer before the due date {{ $data['due_date'] }}.</b>
        <br>
        <b>Hoping for a prompt response.</b>
    </div>
    <div>
        <b>Best Regards,</b>
        <br>
        {{ $data['te_name'] }}
        <br>
        Tender Executive
        <br>
        Mob No.: {{ $data['te_mob'] }}
        <br>
        Mail ID: {{ $data['te_mail'] }}
    </div>
    <br>
    <div>
        Volks Energie Pvt. Ltd.
        <br>
        B1/D8, 2nd Floor,
        <br>
        Mohan Cooperative Industrial Estate,
        <br>
        Mathura Road, New Delhi - 110044
    </div>
</div>
