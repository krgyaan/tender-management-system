<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['team_member'] }},</p>

    <p>Please follow up with the concerned person(s) of {{ $data['organization_name'] }} for
        {{ $data['follow_up_for'] }}.</p>

    <p>Initiate the manual or auto follow-up and update the status using the form link below or your dashboard.</p>

    <a href="{{ $data['form_link'] }}">Form Link</a>

    <p>
        <b>Best Regards,</b>
        <br>{{ $data['follow_up_initiator'] }}
    </p>
</div>
