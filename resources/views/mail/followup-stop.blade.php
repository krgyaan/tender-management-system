<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $followup['assigner'] }},</p>

    <p>The followup for {{ $followup['follow_up_for'] }}, with the team of {{ $followup['organization_name'] }} has been
        stopped.</p>

    <p>The reason is {{ $followup['reason'] == 'Remarks' ? $followup['remarks'] : $followup['reason'] }}</p>
    @if ($followup['reason'] == 'Followup Objective achieved')
        <p>Please find the proofs below or same attachment:</p>
        “{{ $followup['proofs'] }}”
    @else
        <p>We will reinitiate the auto follow-up after a few days of manual follow-up.</p>
    @endif

    <p>
        <b>Best Regards,</b><br>
        {{ $followup['assignee'] }}
    </p>
</div>
