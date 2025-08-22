<x-mail::message>
    Dear Tushar/Accounts team,

    Please prepare the following cheque.

    The purpose of the cheque is {{ $data['purpose'] }},

    Please prepare the cheque using the details below:
    Party Name: {{ $data['partyName'] ?? '' }}
    Cheque Date: {{ $data['chequeDate'] ?? '' }}
    Amount: Rs. {{ $data['amount'] ?? '' }}

    This cheque is required within {{ $data['cheque_needs'] }} Hrs.

    Please make the cheque within the shared time limit and share the Soft copy of the cheque and the Positive pay
    confirmation, if applicable, with us on time using the link below:
    {{ $data['link'] }}

    “Ensuring timely operations is the only source of growth”

    Regards,
    {{ $data['assignee'] }},
    Approved by {{ $data['tlName'] }}
</x-mail::message>
