@extends('layouts.app')
@section('page-title', 'WO Details Add')
@section('content')

<div class="col-md-12">
    <div class="card">
        <!--<div class="card-header">-->
            <!--<h6 class="mb-0">Product Attribute Price</h6>-->
        <!--</div>-->
        <div class="card-body">
            <form method="post" action="{{asset('/admin/wodetailaddpost')}}" enctype="multipart/form-data"
                                  id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                                @csrf
                                
                                <input type="hidden" value="{{$basic_detail_id}}" name="basic_detail_id" >
            
            <div class="row">
                <div class="col-md-12 text-right mt-3">
                    <p id="add_row" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="10" y="4" width="4" height="16" />
                            <rect x="4" y="10" width="16" height="4" />
                        </svg>
                    </p>
                    <p id="delete_row" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="10" width="16" height="4" />
                        </svg>
                    </p>
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                        <thead>
                            <tr>
                                <th class="text-center">Sr.No</th>
                                <th class="text-center">Organization</th>
                                <th class="text-center">Departments</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Designation</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            
                            
                            <tr id="addr0">
                                <td>1</td>
                                <td>
                                    <select name="organization[]" class="form-control" required>
                                        <option value="" disabled selected>Select Organization</option>
                              
                                
                                   @foreach($tender_info as $tdinfo)
                                                         @php
                                   $organization = DB::table('organizations')->where('id',$tdinfo->organisation)->first();
                                  
                                @endphp
                     
                             


                                        <option value="@if(isset($organization)){{$organization->id}} @else null @endif">@if(isset($organization)){{$organization->name}} @else null @endif</option>
                                       @endforeach          
                                         
                                       
                                    </select>
                                </td>
                             
                                
                                <td>
                                    <select name="departments[]" class="form-control" required>
                                        <option value="" disabled selected>Select Departments</option>
                                        <option value="EIC">EIC</option>
                                        <option value="User">User</option>
                                        <option value="C&P">C&P</option>
                                        <option value="Finance">Finance</option>
                                        
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="name[]" placeholder="Enter Name" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" name="designation[]" placeholder="Enter Designation" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" name="phone[]" placeholder="Enter Phone" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                </td>
                                <td>
                                    <input type="email" name="email[]" placeholder="Enter Email" class="form-control" required>
                                </td>
                            </tr>
                            <tr id="addr1"></tr>
                        </tbody>
                    </table>
                </div>
                
                
                
            </div>
            <div class="row" >
            
                <div class="col-md-6 pt-3 ">
        	    	<label for="input28" class="form-label">Budget(Pre GST)</label>
        	    	<div class="input-group">
        	    		
        	    		<select name="par_gst" class="form-control" required>
                                        <option value="" disabled selected>Select Budget</option>
                                        @foreach ($basic as $key => $row) 
                                        <option value="{{ $row->id ?? 'N/L' }}">{{ $row->par_gst ?? 'N/L' }}</option>
                                        @endforeach
                                       
                                    </select>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3" >
        	    	<label for="input28" class="form-label">Max LD%</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="text" class="form-control" name="max_ld" id="input28" placeholder="Max LD" required>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3">
        	    	<label for="input28" class="form-label">LD Start Date</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="date" class="form-control" name="ldstartdate" id="input28" placeholder="Date" required>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3">
        	    	<label for="input28" class="form-label">Max. LD Date</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="date" class="form-control" name="maxlddate" id="input28" placeholder="Date">
        	    	  </div>
        	    </div>
        	    
        	    <div class="col-md-6 pt-3">
                    <label for="input28" class="form-label">PBG Applicable</label>
                    <div class="input-group">
                        <select name="pbg_applicable" class="form-control" id="pbg_select" required>
                            <option value="" disabled selected>Select PBG</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="pt-3" id="input_field" style="display:none;">
                    <label for="input29" class="form-label">Enter Details</label>
                    <input type="file" class="form-control" id="input29" name="file_applicable" >
                </div>
                </div>
                
                
                
               

        	    <div class="col-md-6 pt-3">
                    <label for="input28" class="form-label">Contract Agreement</label>
                    <div class="input-group">
                        <select name="contract_agreement" class="form-control" id="contract_agreement_select" >
                            <option value="" disabled selected>Select Contract</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="pt-3" id="contract_input_field" style="display:none;">
                  
                    <label for="contract_input" class="form-label">Enter Contract Details</label>
                    <input type="file" class="form-control" id="contract_input" name="file_agreement" >
                </div>
                </div>
                
                
                
               

                
        	    <div class="col-md-12 pt-5">
                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </div>
        	    
            </div>
            
            </form>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        var i = 1;

        $("#add_row").click(function () {
            var b = i - 1;
            $('#addr' + i).html($('#addr' + b).html()).find('td:first-child').html(i + 1);
            $('#tab_logic').append('<tr id="addr' + (i + 1) + '"></tr>');
            i++;
        });

        $("#delete_row").click(function () {
            if (i > 1) {
                $("#addr" + (i - 1)).html('');
                i--;
            }
        });

        $('#tab_logic tbody').on('keyup change', function () {
            calc();
        });

        $('#tax').on('keyup change', function () {
            calc_total();
        });
    });
</script>
@endpush


        <script>
                    document.getElementById('pbg_select').addEventListener('change', function() {
                        
                        var inputField = document.getElementById('input_field');
                       
                        if (this.value === '1') {
                           
                            inputField.style.display = 'block';
                        } else {
                            inputField.style.display = 'none';
                        }
                    });
                
                    document.getElementById('contract_agreement_select').addEventListener('change', function() {
                        var contractInputField = document.getElementById('contract_input_field');
                        if (this.value === '1') {
                            contractInputField.style.display = 'block';
                        } else {
                            contractInputField.style.display = 'none';
                        }
                    });
                </script>

@endsection



             
