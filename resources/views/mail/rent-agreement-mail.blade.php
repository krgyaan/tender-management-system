<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear team,</p>

    <p>The rent agreement between <strong>{{ $data['firstparty'] }}</strong> and
        <strong>{{ $data['secondparty'] }}</strong> is expiring in
        <strong>{{ $data['days'] }} days</strong>.
    </p>

    <p>The security deposit against the Rent agreement is Rs. <b>{{ $data['rentamount'] }}</b>.</p>

    <p>If the rent agreement is to be extended, the agreed increment amount is
        <b>{{ $data['rentincrementatexpiry'] }}</b>.
    </p>

    <p>
        Please take immediate action and discuss this with the counterparty immediately.
        If the rent agreement is extended, please upload the New Rent Agreement:
        <a href="{{ $data['email_file'] }}">Update Rent Agreement</a>.
    </p>

    <p>
        Regards, <br>
        {{ $data['coordinator'] }}, <br>
        Volks Energie Pvt. Ltd.
    </p>
</div>
