<div class="container">
    <p>Dear {{ $data['client_details']['name'] }},</p>
    <p>We have carefully reviewed the tender documents for {{ $data['tender_no'] }} floated by your organization. </p>
    <p>
        After careful consideration, we would like to raise the following queries regarding the various clauses in the
        tender documents.
    </p>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <tr style="border: 1px solid #ddd;">
            <th style="width: 30%">Date</th>
            <td>{{ date('d-m-Y') }}</td>
        </tr>
        <tr style="border: 1px solid #ddd;">
            <th>Tender No.</th>
            <td>{{ $data['tender_no'] }}</td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
        <thead>
            <tr style="border: 1px solid #ddd;">
                <th style="width: 10%; text-align: center;">Page No.</th>
                <th style="width: 10%; text-align: center;">Clause No.</th>
                <th style="width: 20%; text-align: center;">Type of Query</th>
                <th style="width: 30%; text-align: center;">Current Statement</th>
                <th style="width: 30%; text-align: center;">Requested Statement</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['queries'] as $query)
                <tr style="border: 1px solid #ddd;">
                    <td style="width: 10%; text-align: center;">{{ $query['page_no'] }}</td>
                    <td style="width: 10%; text-align: center;">{{ $query['clause_no'] }}</td>
                    <td style="width: 20%; text-align: center;">{{ ucfirst($query['query_type']) }}</td>
                    <td style="width: 30%; text-align: center;">{{ $query['current_statement'] }}</td>
                    <td style="width: 30%; text-align: center;">{{ $query['requested_statement'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>
        Please review the above requested clarifications and issue a suitable Corrigendum for this tender. We are
        grateful for the opportunity given by your organization.
    </p>
    <p>
        Best Regards,<br>
        {{ $data['assignee'] }},<br>
        Volks Energie Pvt. Ltd.<br>
        {{ $data['te_mobile'] }}<br>
        {{ $data['te_email'] }}<br>
        {{ $data['ve_address'] }}
    </p>
</div>
