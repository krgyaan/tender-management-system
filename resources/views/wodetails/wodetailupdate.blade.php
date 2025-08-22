@extends('layouts.app')
@section('page-title', 'WO Details Update')
@section('content')


<!--///////new-->


<div class="col-md-12">
    <div class="card">
        <!--<div class="card-header">-->
            <!--<h6 class="mb-0">Product Attribute Price</h6>-->
        <!--</div>-->
        <div class="card-body">
            <form method="post" action="{{asset('/admin/wodetailupdatepost')}}" enctype="multipart/form-data"
                                  id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                                @csrf
                                
                                <input type="hidden" value="{{$wodetails->id}}" name="id" >
            
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
                            
                             @if(is_array($wodetails->name) && count($wodetails->name) > 0)
                            @foreach($wodetails->name as $index => $name)
                            <tr id="addr0">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <select name="organization[]" class="form-control" required>
                                        <option value="" disabled selected>Select Organization</option>
                              
                                  
                                       
                                      @foreach($tender_info as $tdinfo)
                                        @php
                                            $organization = DB::table('organizations')->where('id', $tdinfo->organisation)->first();
                                        @endphp
                                    
                                        <option value="{{ $organization ? $organization->id : 'null' }}" 
                                            @if(isset($wodetails->organization[$index]) && $wodetails->organization[$index] == ($organization ? $organization->id : 'null')) selected @endif>
                                            {{ $organization ? $organization->name : 'null' }}
                                        </option>
                                    @endforeach
                                       
                                    </select>
                                </td>
                             
                                
                                <td>
                                    <select name="departments[]" class="form-control" required>
                                         <option value="" disabled selected>Select Departments</option>
                                        <option value="EIC" @if(isset($wodetails->departments[$index]) && $wodetails->departments[$index] == 'EIC') selected @endif>EIC</option>
                                        <option value="User" @if(isset($wodetails->departments[$index]) && $wodetails->departments[$index] == 'User') selected @endif>User</option>
                                        <option value="C&P" @if(isset($wodetails->departments[$index]) && $wodetails->departments[$index] == 'C&P') selected @endif>C&P</option>
                                        <option value="Finance" @if(isset($wodetails->departments[$index]) && $wodetails->departments[$index] == 'Finance') selected @endif>Finance</option>
                                        
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="name[]" value="{{ $wodetails->name[$index] }}" placeholder="Enter Name" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" name="designation[]" value="{{ $wodetails->designation[$index] }}"  placeholder="Enter Designation" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" name="phone[]" value="{{ $wodetails->phone[$index] }}" placeholder="Enter Phone" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                </td>
                                <td>
                                    <input type="email" name="email[]" value="{{ $wodetails->email[$index] }}" placeholder="Enter Email" class="form-control" required>
                                </td>
                            </tr>
                             @endforeach
                            @endif
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
                                        <option value="{{ $row->id ?? 'N/L' }}" {{ $row->id == $wodetails->par_gst ? 'selected' : '' }}>{{ $row->par_gst ?? 'N/L' }}</option>
                                        @endforeach
                                      
                                    </select>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3" >
        	    	<label for="input28" class="form-label">Max LD%</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="text" class="form-control" value="{{$wodetails->max_ld}}" name="max_ld" id="input28" placeholder="Max LD" required>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3">
        	    	<label for="input28" class="form-label">LD Start Date</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="date" class="form-control" value="{{$wodetails->ldstartdate}}" name="ldstartdate" id="input28" placeholder="Date" required>
        	    	  </div>
        	    </div>
                <div class="col-md-6 pt-3">
        	    	<label for="input28" class="form-label">Max. LD Date</label>
        	    	<div class="input-group">
        	    		
        	    		<input type="date" class="form-control" value="{{$wodetails->maxlddate}}" name="maxlddate" id="input28" placeholder="Date">
        	    	  </div>
        	    </div>
        	    
        	    <div class="col-md-6 pt-3">
                    <label for="input28" class="form-label">PBG Applicable</label>
                    <div class="input-group">
                        <select name="pbg_applicable" class="form-control" id="pbg_select" required>
                           <option value="" disabled {{ is_null($wodetails->pbg_applicable) ? 'selected' : '' }}>Select PBG</option>
                            <option value="1" {{ $wodetails->pbg_applicable_status == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $wodetails->pbg_applicable_status == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="pt-3" id="input_field" style=" @if(isset($wodetails->contract_agreement_status) && $wodetails->contract_agreement_status == 1)
                            display: block;
                        @else
                            display: none;
                        @endif 
                        position: relative;>
                        <label for="input29" class="form-label">Enter Details</label>
                        <input type="file" class="form-control" id="input29" name="file_applicable">
                                <div style="position: absolute; top: 73%; right: 20px; transform: translateY(-50%); z-index: 1;">
                                        <a href="{{ asset('uploads/applicable/' . $wodetails->file_applicable) }}" data-fancybox="image" data-caption="">
                                            <img src="{{ asset('uploads/applicable/' . $wodetails->file_applicable) }}" alt="image" style="height: 20px;width: 40px;">
                                        </a>
                                    </div>
                    </div>
                </div>
                
                
                
               

        	    <div class="col-md-6 pt-3">
                    <label for="input28" class="form-label">Contract Agreement</label>
                    <div class="input-group">
                        <select name="contract_agreement" class="form-control" id="contract_agreement_select" required>
                             <option value="" disabled {{ is_null($wodetails->pbg_applicable) ? 'selected' : '' }}>Select Contract</option>
                            <option value="1" {{ $wodetails->contract_agreement_status == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $wodetails->contract_agreement_status == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="pt-3" id="contract_input_field" style=" 
                        @if(isset($wodetails->contract_agreement_status) && $wodetails->contract_agreement_status == 1)
                            display: block;
                        @else
                            display: none;
                        @endif 
                        position: relative;">                 
                    <label for="contract_input" class="form-label">Enter Contract Details</label>
                    <input type="file" class="form-control" id="contract_input" name="file_agreement" >
                        <div style="position: absolute; top: 73%; right: 20px; transform: translateY(-50%); z-index: 1;">
                                        <a href="{{ asset('uploads/applicable/' . $wodetails->file_agreement) }}" data-fancybox="image" data-caption="">
                                            <img src="{{ asset('uploads/applicable/' . $wodetails->file_agreement) }}" alt="image" style="height: 20px;width: 40px;">
                                        </a>
                                    </div>
                </div>
                </div>
                
                
                
               

                
        	    <div class="col-md-12 pt-5">
                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Update</button>
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



             
