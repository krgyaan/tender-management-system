	<!DOCTYPE html>
	<html>
	<head>
	<title>project</title>
<style>
	.project{
	padding: 13px 0px;
	text-align: center;
	font-size: 18px;
	font-weight: 600;
	border: 1px solid #dee2e6;
	background-color: #ffc10769;
	 width: 100%;
	

	}
	</style>
	</head>
	<body>







	<table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6;" class="table table-bordered">
	
	<tbody>
	      @foreach ($wodata as $data)
	      
	      
<tr>
  <td colspan="100%" style="text-align: center; padding: 13px 0px; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6; background-color: #ffc10769;">
    Project summary sheet
  </td>
</tr>
	
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;" >
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;" >Project Name</th>
	<th></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">WO No</th>
	<th></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">WO Date</th>
	<th></th>

	</tr >


	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>




	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">

	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Dapartment</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Name</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Designation</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Phone</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Email</th>

	</tr>
	</tr>
             
          
@if(isset($data['name']) && is_array($data['name']))
    @foreach($data['name'] as $index => $name)
        <tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
            <td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{ $data['departments'][$index] }}</td>
            <td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{ $name }}</td>
            <td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{ $data['designation'][$index] }}</td>
            <td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{ $data['phone'][$index] }}</td>
            <td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{ $data['email'][$index] }}</td>
        </tr>
    @endforeach
@else
    <p>No projects found.</p>
@endif

 
                
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">WO Value (pre GST)</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;"></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">WO Value (GST Amount)</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;"></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Budget</th>
	</tr>

	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{$data['par_gst']}}</td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>


	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Total</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">"=Supply + I$C</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Supply</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;"></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">I$C</th>
	</tr>
	
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Max LD%</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;"></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">LD Start Date</th>
	<th></th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">Max LD Date</th>
	<th style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;"></th>
	</tr>

<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{$data['max_ld']}}</td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{$data['ldstartdate']}}</td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">{{$data['maxlddate']}}</td>
	
	</tr>

	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Original LOA/Gem PO/LOI/Draft PO</th>


	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">
	    <img src="{{ asset('upload/basicdetails/') }}" alt="Image" style="max-width: 100px; max-height: 100px;">
	</td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
	<th scope="row"style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Contact Agreement Format</th>


	</tr>
		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;">
	    <img src="{{ asset('uploads/applicable/.$data->file_agreement') }}" alt="Image" style="max-width: 100px; max-height: 100px;">
	</td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Filled BG Format</th>


	</tr>

		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Scope of Work</th>
	</tr>

		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Tech specs</th>
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>


	<tr>
	<th scope="row"style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">Detailed BOQ</th>
	</tr>

		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row"style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">MAF Formet</th>
	</tr>

		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>
	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">MAF From Vendor</th>
	</tr>
		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>


	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">MII Formet</th>
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row"style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">MII From Vendor</th>
	</tr>
		<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	<td style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;height: 30px;"></td>
	
	</tr>

	<tr>
	<th scope="row" style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;background-color: #ffc10769;">List of Documents from OEM</th>
	</tr>
	<tr style="text-align: center; font-size: 18px; font-weight: 600; border: 1px solid #dee2e6;">
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	</tr>


@endforeach

	</tbody>


	</table>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
	</body>
	</html>
