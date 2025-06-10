<div>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
    <p>Dear {{ $data['client_name'] }},</p>

    <p>Our Company, M/s. Volks Energie Pvt. Ltd. is bidding for {{ $data['tender_no'] }}, due on
        {{ $data['due_date'] }}.</p>

    <p>
        Please find attached the soft copy of the physical documents to be submitted.
        We have couriered the documents via {{ $data['courier_provider'] }}, against docket no. {{ $data['docket_no'] }}
    </p>

    <p>Expected Delivery time is {{ $data['delivery_time'] }}</p>

    <p>Also, attach the Docket slip for the courier, so that the courier can be tracked and is not lost in transit.</p>

    <p><b>Best Regards,</b></p>
    {{ $data['tender_executive'] }}

</div>
