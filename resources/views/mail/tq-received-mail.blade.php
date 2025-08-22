<div>
    <p>Dear {{ $data['te'] }},</p>

    <p>A TQ has been received for {{ $data['tender_name'] }} ({{ $data['tender_no'] }}).</p>
    <p>The Tender due date and time is {{ $data['due'] }}.</p>

    <p>The queries requested are:</p>

    @if (!empty($data['tqData']))
        <table style="width: 100%; border-collapse: collapse;" border="1" cellpadding="6">
            <thead>
                <tr>
                    <th style="width: 25%; background-color: #f2f2f2;">TQ Type</th>
                    <th style="width: 75%; background-color: #f2f2f2;">Query Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['tqData'] as $item)
                    <tr>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ $item['desc'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No queries available.</p>
    @endif


    <p>Please ensure the timely reply to the queries.</p>

    <p>Best Regards,<br>{{ $data['coordinator'] }}</p>
</div>
