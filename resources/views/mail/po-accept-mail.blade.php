<div>
    Dear team,
    <p>
        We thank you for placing your trust in us and releasing the WO no. {{ $data['number'] }}, dated
        {{ $data['date'] }}.
    </p>
    <p>
        After carefully reviewing the order, we accept the WO as shared by you. Please find the signed and Accepted copy
        of the WO attached to this mail. Also the same has been sent via courier to your office.
    </p>
    <p>
        @if ($data['is_contract'] && $data['is_pbg'])
            Also attached are the filled Contract Agreement format and filled PBG Format for your review and feedback.
        @elseif ($data['is_contract'])
            Also attached is the filled Contract Agreement format for your review and feedback.
        @elseif ($data['is_pbg'])
            Also attached is the filled PBG Format for your review and feedback.
        @endif
    </p>
    <p>
        We would like to discuss the complete project via an online meeting, we would like to introduce our team as well
        as discuss the process for document approval, other formalities, and the project timelines during the meeting.
    </p>
    <p>
        Please suggest a suitable time for a kickoff meeting and also the names and email addresses of the members
        joining from your end so that we can share the meeting link with them too.
    </p>
    <p>We are grateful for the opportunity given by your organization.</p>

    <p>Best Regards,</p>
    <p>{{ $data['te_name'] }},</p>
    <p>Volks Energie Pvt. Ltd.</p>
    <p>{{ $data['te_mob'] }}</p>
    <p>{{ $data['te_mail'] }}</p>
    <p>B1/D8 2nd Floor, <br>Mohan Co-Operative Industrial Estate, <br>New Delhi - 110044</p>
</div>
