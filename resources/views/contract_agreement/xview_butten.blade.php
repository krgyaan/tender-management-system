@extends('layouts.app')
@section('page-title', 'Contract Details ')
@section('content')


<!--///////new-->


<div class="col-md-12">
    <div class="card">
        <!--<div class="card-header">-->
            <!--<h6 class="mb-0">Product Attribute Price</h6>-->
        <!--</div>-->
        <div class="card-body">
          
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                <thead>
                     <tr>
                        <h6 style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;text-align: center;">Wo Details</h6>
                    </tr>
                    <tr style="text-align: center ;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">Date Time</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{ \Carbon\Carbon::parse($wodetails->meeting_date_time)->format('d-M-Y') }} </th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{ \Carbon\Carbon::parse($wodetails->meeting_date_time)->format('h:i A') }}</th>
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">PO/WO No.</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;"><a href="" >{{$wodetails->google_meet_link}}</a></th>
                    </tr>
                </thead>
                
                <thead>
                     <tr style="text-align: center ;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;"></th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;"> </th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;"> </th>
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">MOM</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">
                            <a href="{{ asset('uploads/applicable/' . $wodetails->upload_mom) }}" data-fancybox="image" data-caption="">
                                 <img src="{{ asset('uploads/applicable/' . $wodetails->upload_mom) }}" alt="image" style="height: 30px;width: 40px;">
                            </a>
                             </th>
                        
                    </tr>
                     <tr style="text-align: center ;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">Cntract Agreement</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">
                            <a href="{{ asset('uploads/applicable/' . $wodetails->contract_agreement) }}" data-fancybox="image" data-caption="">
                                 <img src="{{ asset('uploads/applicable/' . $wodetails->contract_agreement) }}" alt="image" style="height: 30px;width: 40px;">
                            </a>
                             </th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;"> </th>
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">Client Signed</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">
                            <a href="{{ asset('uploads/applicable/' . $wodetails->client_signed) }}" data-fancybox="image" data-caption="">
                                 <img src="{{ asset('uploads/applicable/' . $wodetails->client_signed) }}" alt="image" style="height: 30px;width: 40px;">
                            </a>
                             </th>
                        
                    </tr>
                    <tr style="background-color: #FF971D; text-align: center;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;">Departments</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Name</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Designation</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Phone</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
                    </tr>
                    
                   
                    
                    
                </thead>
                <tbody>
                @foreach($wodetails->departments as $key => $department)
                    <tr style="background-color: #00000091;">
                        <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                            {{ $department }}
                        </td>
                        <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                            {{ $wodetails->name[$key] ?? '' }}
                        </td>
                        <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                            {{ $wodetails->designation[$key] ?? '' }}
                        </td>
                        <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                            {{ $wodetails->phone[$key] ?? '' }}
                        </td>
                        <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                            {{ $wodetails->email[$key] ?? '' }}
                        </td>
                    </tr>
                @endforeach
                            
                </tbody>
            </table>
      
        <br>
        <br>
        <div>
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                
                <thead>
                     <tr>
                        <h6 style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;text-align: center;">If YES, then Wo Amendment Table</h6>
                    </tr>
                    <tr style="text-align: center ;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">Date</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{ \Carbon\Carbon::parse($basic->date)->format('d-M-Y') }}</th>
                        <th style="padding: 10px; border: 1px solid #ddd;background-color: #FF971D;">PO/WO No.</th>
                        <th style="text-align: center; padding: 8px;border: 1px solid #ddd;background-color: #00000091;">{{$basic->number}}</th>
                    </tr>
                </thead>
                <thead>
                    
                   
                    <tr style="background-color: #FF971D; text-align: center;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;">Page No</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Clause No</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Present Text</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Corrected Text</th>
                    </tr>
                </thead>
                <tbody>
                        @if($acceptance_yes && isset($acceptance_yes->page_no) && count($acceptance_yes->page_no) > 0)
                            @foreach($acceptance_yes->page_no as $key => $page)
                                <tr style="background-color: #00000091;">
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                                        {{ $page }}
                                    </td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                                        {{ $acceptance_yes->clause_no[$key] ?? '' }}
                                    </td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                                        {{ $acceptance_yes->current_statement[$key] ?? '' }}
                                    </td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                                        {{ $acceptance_yes->corrected_statement[$key] ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 8px; border: 1px solid #ddd; ">
                                                No data available.
                                            </td>
                                        </tr>
                                    @endif  
                     <tr style="background-color: #FF971D; text-align: center;">
              
                        <th style="padding: 10px; border: 1px solid #ddd;">Budget(Pre GST)</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Max LD%</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">LOA/GEM/LOI/Draft WO</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">FOA/SAP PO/Detailed WO</th>
                    </tr>
                    <tr style="background-color: #00000091;">
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; "> {{$basic->par_gst}}</td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; "> {{$wodetails->max_ld}}</td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; "> <a href="{{ asset('upload/basicdetails/' . $basic->image) }}" data-fancybox="image" data-caption="">
                                                                <img src="{{ asset('upload/basicdetails/' . $basic->image) }}" alt="image" style="height: 30px;width: 40px;">
                                                            </a></td>
                                    <td style="text-align: center; padding: 8px; border: 1px solid #ddd; "><a href="{{ asset('upload/basicdetails/' . $basic->foa_sap_image) }}" data-fancybox="image" data-caption="">
                                                                <img src="{{ asset('upload/basicdetails/' . $basic->foa_sap_image) }}" alt="image" style="height: 30px;width: 40px;">
                                                            </a></td>
                    </tr>                
                   </tbody>
            </table>
        </div>
        
        
        
          
                
                                        

                
            </div>
    
           
            
        </div>
    </div>
</div>







       

@endsection



             
