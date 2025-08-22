<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    <p>
        I have reviewed the Tender Info sheet and the Tender documents and decided
        @if ($data['tlStatus'] == 1)
            to bid for the tender
        @elseif ($data['tlStatus'] == 2)
            to not bid for this tender
        @elseif ($data['tlStatus'] == 3)
            to review the Tender info sheet again.
        @endif
    </p>

    <p>
        <b>Remarks: </b><br>
        @if ($data['tlStatus'] == 3)
            {{ $data['remarks'] }}
        @endif
        @if ($data['tlStatus'] == 2)
            {{ $data['rej_remark'] }}
        @endif
    </p>

    @if ($data['tlStatus'] == 1)
        <p>Please do the necessary next steps:</p>
        <ul>
            <li>Request EMD and Tender fees from - <a href="{{ $data['emdLink'] }}">EMD Link</a> and <a href="{{ $data['tenderFeesLink'] }}">Tender Fees Link</a>.</li>
            <li>The selected mode of Tender Fees: {{ $data['tenderFeesMode'] }}</li>
            <li>The selected mode of EMD: {{ $data['emdMode'] }}</li>
            <li>Raise RFQ <a href="{{ $data['rfqLink'] }}">Here</a> to {{ $data['vendor'] }}</li>
            <li>Courier Physical Documents: {{ $data['phyDocs'] }}</li>
        </ul>
        <p>PQR Selection is {{ $data['pqr'] == 1 ? 'approved' : 'Rejected, please use the revised PQR documents' }}.</p>
        <p>Finance Doc Selection is {{ $data['fin'] == 1 ? 'approved' : 'Rejected, please use the revised Finance Docs' }}.</p>
        @elseif ($data['tlStatus'] == 3)
            <p>Please complete the tender info form and resend it.</p>
        @elseif ($data['tlStatus'] == 2)
            <p>Please avoid this tender and move on to the next available tender.</p>
        @endif

    <p>
        <b>Regards,</b><br>
        {{ $data['tlName'] }}
    </p>
</div>
