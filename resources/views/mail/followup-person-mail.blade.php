<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    {{ 'Days Outstanding: ' . $data['since'] }}
    <br>
    {{ 'Reminder Number: ' . $data['reminder'] }}
    <br>
    {!! $data['mail'] !!}
</div>
