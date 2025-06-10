<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Display</title>
</head>
<body>
    



@foreach($meeting as $item)
   
   
    
    <div style="padding: 0px 30px" >
    <h3>Dear Sir ,</h3>
    <p style="margin-top: -13px;" >As discussed with you, we have scheduled the Kick off meeting on {{ \Carbon\Carbon::parse($item->meeting_date_time)->format('d-M-Y') }} at {{ \Carbon\Carbon::parse($item->meeting_date_time)->format('h:i A') }}. <br> We would linl to introduce our team as well as discuss the process for document approva, other formalitis, and the project timelines during the meering </p>
    <p style="" >Please use the link below to join the meeting <br> <a href="{{$item->google_meet_link}}">{{$item->google_meet_link}} </a> </p>
    <p style="" > Best Regards, </p>
    <p style="margin-top: -13px;" > Tender Executive, </p>
    <p style="margin-top: -13px;" > Volks Energie Pvt. Ltd. </p>
    <p style="" > TE Mobile no.</p>
    <p style="margin-top: -13px;" > TE Email id. </p>
    <p style="" > VE Address </p>
  
        
        
        
        
    </div>
@endforeach
</body>
</html>
