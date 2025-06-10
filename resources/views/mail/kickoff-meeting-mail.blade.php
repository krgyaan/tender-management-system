<div>
    <p>Dear Sir,</p>
    <p>
        As discussed with you, we have scheduled the Kick off meeting on {{ $data['date'] }} at
        {{ $data['time'] }}.
        We would like to introduce our team as well as discuss the process for document approval, other formalities, and
        the project timelines during the meeting.
    </p>
    <p>
        Please use the link below to join the meeting:<br>
        <a href="{{ $data['link'] }}">{{ $data['link'] }}</a>
    </p>

    <p>Best Regards,</p>
    <p>{{ $data['te_name'] }},
        <br>Volks Energie Pvt. Ltd.
        <br>{{ $data['te_mob'] }}
        <br>{{ $data['te_mail'] }}
        <br>B1/D8 2nd Floor,
        <br>Mohan Co-Operative Industrial Estate,
        <br>New Delhi - 110044
    </p>
</div>
