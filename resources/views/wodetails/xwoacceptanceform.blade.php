@extends('layouts.app')
@section('page-title', 'WO Acceptance')
@section('content')

<div class="col-md-12">
    <div class="card">
        <!--<div class="card-header">-->
            <!--<h6 class="mb-0">Product Attribute Price</h6>-->
        <!--</div>-->
        <div class="card-body">
            <form method="post" action="{{asset('/admin/woacceptanceformpost')}}" enctype="multipart/form-data"
                                  id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                                @csrf
                                
                                <input type="hidden" value="{{$basic_detail_id}}" name="basic_detail_id" >
            
          <div class="row">
                <div class="col-md-6 pt-3">
                    <label for="input55" class="form-label">Wo Amendment Needed</label>
                    <div class="input-group">
                        <select name="amendment_needed" class="form-control" id="pbg_applicable" required >
                            <option value="" disabled selected>Select</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                
                <div id="file_inputs" class="col-md-12 pt-3" style="display: none;">
                    <br>
                    <div class="row" >

                        <div class="mb-2 col-md-4 ">
                            <label class="form-label">Accepted , Initiate</label>
                                <input type="file" class="form-control" name="accepted_initiate" accept="image/*,application/pdf" >
                            </div>
                            
                            <div class="mb-2 col-md-4">
                                <label class="form-label">Upload Accepted and Signed Copy</label>
                                <input type="file" class="form-control" name="accepted_signed" accept="image/*,application/pdf" >
                            </div>
                            <div class="mb-2 col-md-4">
                                <label class="form-label"> Accepted And Signed Copy </label>
                                <input type="file" class="form-control" name="accepted_and_signed" accept="image/*,application/pdf" >
                            </div>
                    </div>
                </div>
                
                <div>
                    <div class="col-md-12 text-right mt-3" id="buttons" style="display: none;">
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
                
                    <div class="col-md-12" id="data_row" style="display: none;">
                        <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                            <thead>
                                <tr>
                                    <th class="text-center">Sr.No</th>
                                    <th class="text-center">Page No</th>
                                    <th class="text-center">Clause No</th>
                                    <th class="text-center">Current Statement</th>
                                    <th class="text-center">Corrected Statement</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="addr0">
                                    <td>1</td>
                                   
                                    <td>
                                        <input type="text" name="page_no[]" placeholder="Enter Page No" class="form-control" >
                                    </td>
                                    <td>
                                        <input type="text" name="clause_no[]" placeholder="Enter Clause No" class="form-control" >
                                    </td>
                                    
                                    <td>
                                        <input type="text" name="current_statement[]" placeholder="Enter Current Statement" class="form-control" >
                                    </td>
                                    <td>
                                        <input type="text" name="corrected_statement[]" placeholder="Enter Corrected Statement" class="form-control" >
                                    </td>
                                </tr>
                                <tr id="addr1"></tr>
                            </tbody>
                        </table>
                    </div>
                   
                    <div class="row" >    
                       <div class="col-md-6 pt-3" id="data_row2" style="display: none;">
                            <label for="input28" class="form-label">Followup Frequency</label>
                            <div class="input-group">
                                <select name="followup_frequency" class="form-control" id="pbg_applicable_2" >
                                    <option value="" disabled selected>Select</option>
                                    <option value="daily">Daily</option>
                                    <option value="alternate">Alternate Days</option>
                                    <option value="weekly">Weekly (every Monday)</option>
                                    <option value="stop">Stop</option>
                                </select>
                            </div>
                        </div>
                        
                       
                        <div class="col-md-6 pt-3"  id="stop_inputs"  style="display: none;">
                           <label for="select_field" class="form-label">Stop Opsans</label>
                                    <select class="form-control" name="stop_opsans" id="stop_opsans" >
                                        <option value="" disabled selected>Select</option>
                                        <option value="stop">The person is getting angry</option>
                                        <option value="image_text">Followup Objective a achieved</option>
                                        <option value="remark">Remarks</option>
                                    </select>
                         
                        </div>
                        
                        
                        <div id="text_input_field" class="col-md-6 pt-3" style="display: none;">
                                    <label for="text_proof" class="form-label">Enter Text Proof</label>
                                    <input type="text" class="form-control" name="text_proof" placeholder="Enter text proof">
                                </div>
                                
                                <div id="file_input_field" class="col-md-6 pt-3" style="display: none;">
                                    <label for="file_proof" class="form-label">Upload Image Proof</label>
                                    <input type="file" class="form-control" name="file_proof" accept="image/*">
                                </div>
                        </div>
                        <div class="col-md-12 pt-3" id="remarks_field" style="display: none;">
                            <label for="remarks" class="form-label">Enter Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter your remarks"></textarea>
                        </div>
                        
                    </div>  
                        
                    <div class="col-md-12 pt-5">
                        <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </div>
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

 
        $('#tab_logic tbody').on('keyup change', 'input', function () {
            calc(); 
        });

      
        $('#tax').on('keyup change', function () {
            calc_total(); 
        });
    });
</script>
@endpush


            <script>
                document.getElementById('pbg_applicable').addEventListener('change', function() {
                    var dataRow = document.getElementById('data_row');
                    var dataRow2 = document.getElementById('data_row2');
                    var buttons = document.getElementById('buttons');
                    var stop_inputs = document.getElementById('stop_inputs');
                    var text_input_field = document.getElementById('text_input_field');
                    var file_input_field = document.getElementById('file_input_field');
                    var remarks_field = document.getElementById('remarks_field');
                       var fileInputs = document.getElementById('file_inputs');
                    if (this.value == '1') {
                        dataRow.style.display = 'block';  
                        dataRow2.style.display = 'block';  
                        buttons.style.display = 'block';  
                        
                         fileInputs.style.display = 'none';
                    }else if (this.value == '0') {
                        
                        dataRow.style.display = 'none';
                        dataRow2.style.display = 'none';
                        buttons.style.display = 'none';
                         stop_inputs.style.display = 'none'; 
                         text_input_field.style.display = 'none'; 
                         file_input_field.style.display = 'none'; 
                         remarks_field.style.display = 'none'; 
                        fileInputs.style.display = 'block';
               
                    } else {
                        dataRow.style.display = 'none';   
                        dataRow2.style.display = 'none';   
                        buttons.style.display = 'none';   
                        stop_inputs.style.display = 'none';  
                        text_input_field.style.display = 'none';  
                        file_input_field.style.display = 'none';  
                        remarks_field.style.display = 'none';  
                        fileInputs.style.display = 'none';
                    }
                });
                
                
                
                
               document.getElementById('pbg_applicable_2').addEventListener('change', function() {
                    var stopInputs = document.getElementById('stop_inputs');
                    var textInput = document.getElementById('text_input_field');
                    var fileInput = document.getElementById('file_input_field');
                    var remarksField = document.getElementById('remarks_field');
                
                    if (this.value === 'stop') {
                        stopInputs.style.display = 'block';
                    } else {
                        stopInputs.style.display = 'none';
                        textInput.style.display = 'none';
                        fileInput.style.display = 'none';
                        remarksField.style.display = 'none'; 
                    }
                });
                
              
               
                
                
                
            </script>
            
           
            
            <script>
                  document.getElementById('stop_opsans').addEventListener('change', function () {
                        var textInput = document.getElementById('text_input_field');
                        var fileInput = document.getElementById('file_input_field');
                        var remarksField = document.getElementById('remarks_field');
                    
                        if (this.value === 'image_text') {
                            textInput.style.display = 'block';  
                            fileInput.style.display = 'block';  
                            remarksField.style.display = 'none';  
                        } else if (this.value === 'remark') {
                            textInput.style.display = 'none';
                            fileInput.style.display = 'none';
                            remarksField.style.display = 'block';  
                        } else {
                            textInput.style.display = 'none';
                            fileInput.style.display = 'none';
                            remarksField.style.display = 'none';
                        }
                    });
                </script>
           


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
                
                
                
                
                <script>
                document.querySelector("form").addEventListener("submit", function(event) {
                    var selectField = document.getElementById("pbg_applicable");
                    
                    if (!selectField.value) {
                        alert("Please select Yes ya No.");  
                        selectField.focus(); 
                        event.preventDefault(); 
                    }
                
                </script>
                
                
                
                

@endsection



             
