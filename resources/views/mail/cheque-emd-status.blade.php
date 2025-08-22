<x-mail::message>
    Dear {{ $data['assignee'] }},

    The request for Cheque creation has been {{ $data['status'] }}

    @if ($data['status'] == 'Accepted')
        Soft copy of the cheque: attached below.
        Remarks: {{ $data['remark'] }}
    @else
        Reason for rejection:
        {{ $data['reason'] }}
    @endif

    Ensuring all cheques are cleared on time builds the company's credibility.

    Regards,
    Tushar,
    Accounts team.
</x-mail::message>
