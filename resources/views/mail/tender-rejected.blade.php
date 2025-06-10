<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear Coordinator,</p>

    <p>
        This is to inform you that the Tender Id: "{{ $data['tenderNo'] }}"
        has been rejected by the team due to the following reason:
        {{ $data['reason'] }}
    </p>

    <p>
        These are the remarks against this tender:
        {{ $data['remarks'] }}
    </p>

    <p>Please update the tender status accordingly.</p>

    <div>
        Regards,<br>
        {{ $data['assignee'] }},<br>
        Volks Energie
    </div>
</div>
