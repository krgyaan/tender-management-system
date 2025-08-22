<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Display</title>
</head>
<body>
    

@php
    $basicDetails = App\Models\Basic_detail::whereIn('id', array_column($wodata, 'basic_detail_id'))->get()->keyBy('id');
@endphp

@foreach($wodata as $item)
    @php
        $basicdata = $basicDetails[$item['basic_detail_id']] ?? null;
    @endphp 
   
    
    <div style="padding: 0px 10px" >
        <h3 style="color: white;" >PO Acceptance Mail to Client</h3>
        <p style="margin-top: -13px;" > We thank you for placing your trust in us and releasing the WO no. {{ $basicdata->number ?? 'N/A' }}, Date {{ $basicdata->date ?? 'N/A' }} .</p>
        <p style="color: white;" > After carefully reviewing the order, we identified a few clauses requiring amendment. Please find these in the table below: </p>
        <p style="color: white;" > Also attached, is the filled Contract Agreement format and filled PBG Format for your review and feedback. (This line will be there only if the Contract Agreement and PBG are applicable, whichever is not applicable will be removed, discuss with me, if you do not have clarity) </p>
   
        <p style="color: white;" >We would like to discuss the complete project via an online meeting, we would like to introduce our team as well as discuss the process for document approval, other formalities, and the project timelines during the meeting.</p>
        <p style="color: white;" > Please suggest a suitable time for a kickoff meeting and also the names and email addresses of the members joining from your end so that we can share the meeting link with them too.</p>
        <p style="color: white;" >We are grateful for the opportunity given by your organization.</p>
        
        <p style="color: white;" > Best Regards, </p>
        <p style="margin-top: -13px;color: white;" > Tender Leader, </p>
        <p style="margin-top: -13px;color: white;" > Volks Energie Pvt. Ltd. </p>
        <p style="color: white;" > TE Mobile no.</p>
        <p style="margin-top: -13px;color: white;" > TE Email id. </p>
        <p style="color: white;" > VE Address </p>
        
        <br>
        <p style="color: white;" > Attachment: </p>
        <div style="display: flex; gap: 10px;">
            <img src="{{ asset('uploads/acceptance/' . $item->accepted_initiate) }}" alt="Random Image" style="height: 100px; width: 100px; object-fit: cover;">
            <img src="{{ asset('uploads/acceptance/' . $item->accepted_signed) }}" alt="Random Image" style="height: 100px; width: 100px; object-fit: cover;">
            <img src="{{ asset('uploads/acceptance/' . $item->accepted_and_signed) }}" alt="Random Image" style="height: 100px; width: 100px; object-fit: cover;">
        </div>
        
        
        
        
    </div>
@endforeach
</body>
</html>
