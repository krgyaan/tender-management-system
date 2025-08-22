<div>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
        }
    </style>
    <p>Dear {{ $data['assignee'] }},</p>

    @if ($data['changed'] != '')
        @php
            $changedFields = explode(', ', $data['changed']);
            $isTeamMemberChange = in_array('Team Member', $changedFields);
            $isTeamChange = in_array('Team', $changedFields);
            $isTenderNoChange = in_array('Tender Number', $changedFields);
        @endphp

        @if ($isTeamMemberChange)
            <p>Congratulations! A new tender has been assigned to you.</p>
        @elseif ($isTeamChange)
            <b>The team for this tender has been changed.</b>
        @elseif ($isTenderNoChange)
            <b>The tender number has been updated.</b>
        @else
            <p>The following changes have been made to your tender:</p>
            <ul>
                @foreach ($changedFields as $field)
                    <li>{{ $field }} has been updated</li>
                @endforeach
            </ul>
        @endif
    @else
        <p>Congratulations! A new tender has been assigned to you.</p>
    @endif

    <p>Here are the tender details:</p>
    <ul>
        <li>Tender Name : {{ $data['tenderName'] }}</li>
        <li>Tender is to be bid on: {{ $data['website'] }}</li>
        <li>Tender ID.: {{ $data['tenderNo'] }}</li>
        <li>Due Date & Time: {{ $data['due_date'] }}, {{ $data['due_time'] }}</li>
        <br>
        <li>Tender Value: {{ $data['tenderValue'] }}</li>
        <li>Tender Fees: {{ $data['tenderFees'] }}</li>
        <li>EMD: {{ $data['emd'] }}</li>
        @if ($data['remarks'])
            <li>Remarks: {{ $data['remarks'] }}</li>
        @endif
    </ul>

    <p>
        The tender documents are attached to the mail. Please submit the
        <a href="{{ $data['tenderInfoSheet'] }}">Tender Info Sheet</a>
        for your team leader's approval of the tender.
    </p>

    <p>If approved, please ensure that we bid the tender 48 hours before the due date and time.</p>
    <i>"Each new tender is an opportunity for growth"</i>

    <p>
        <b>Regards,</b><br>
        {{ $data['coordinator'] }}<br>
        Volks Energie Pvt. Ltd.
    </p>
</div>
