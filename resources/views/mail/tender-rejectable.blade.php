<x-mail::message>
    Dear Coordinator,

    This is to inform you that the Tender Id: "{{ $data['tenderNo'] }}"
    has been rejected by the team due to the following reason:
    {{ $data['reason'] }}

    These are the remarks against this tender:
    {{ $data['remarks'] }}

    Please update the tender status accordingly.

    Regards,
    {{ $data['assignee'] }},
    Volks Energie
</x-mail::message>
