<div>
    <p>Dear {{ $data['te_name'] }},</p>
    <p>
        The costing of the Tender {{ $data['tender_name'] }}, has been edited and approved for bidding. I have prepared
        the tender documents with the best of my understanding of the technical documents, the scope of work, and the
        quotations received from the vendor. Please submit the Tender using the costing sheet.
    </p>
    <p>Link: <a href="{{ $data['costing_sheet_link'] }}">Costing sheet</a></p>
    <p>
        The tender due date and time is {{ $data['due_date_time'] }}. Please submit the tender at least 24 hours before
        the tender due date and time.
    </p>

    <p>Best Regards,<br>{{ $data['tl_name'] }}</p>
</div>
