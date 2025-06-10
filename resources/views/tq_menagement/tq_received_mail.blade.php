<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Display</title>
</head>

<body>

    @foreach ($data as $item)
        @php
            $tender = DB::table('tender_infos')->where('id', $item->tender_id)->first();
        @endphp
        <div style="padding: 0px 30px">
            <p><b>From:</b> 123 <br>
                <b>To:</b> 123, <br>
                <b>CC:</b> 123,123,123, <br>
                <b>Subject:</b> TQ received - {{ $tender->tender_name }},
            </p>
            <h3>Dear ,</h3>
            <p style="margin-top: -13px;">A TQ has been received for {{ $tender->tender_name }} . <br> The TQ due date
                and time is {{ $tender->due_date }} , {{ $tender->due_time }} </p>
            <p style="">The queries requested are: </p>
            <p style=""> TQ Table </p>
            <p style=""> Please ensure the timely reply to the queries . </p>
            <p style=""> Best Regards, <br> Coordinator </p>
            <p style=""> Attachments:
            <ul>
                <li>TQ Document (if Any)</li>
            </ul>
            </p>
        </div>
    @endforeach
</body>

</html>
