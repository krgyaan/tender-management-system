@extends('layouts.app')
@section('page-title', 'WO Update')
@section('content')

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{asset('/admin/woupdate_post')}}" enctype="multipart/form-data"
                  id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                @csrf
                <input type="hidden" value="{{$basic_data->id}}" name="id" >
             
                <div class="col-md-6 pt-3">
                    <label for="logem_img" class="form-label">LO/GEM/LOI/Draft WO:</label>
                    <div class="input-group">
                        <select name="lo_gem_loi" class="form-control" id="logem_img" required>
                            <option value="" disabled selected>Select</option>
                            <option value="0">If The PO Amendment needed is No</option>
                            <option value="1">If The PO Amendment needed is Yes</option>
                        </select>
                    </div>
                </div>
                
              
                <div id="lo_gem_img" class="col-md-6 pt-3" style="display: none;position: relative;">
                    <label for="file_proof" class="form-label">LO/GEM/LOI/Draft WO:</label>
                    <input type="file" class="form-control" name="lo_gem_img"  >
                    <div style="position: absolute; top: 73%; right: 20px; transform: translateY(-50%); z-index: 1;">
                                        <a href="{{ asset('upload/basicdetails/' . $basic_data->image) }}" data-fancybox="image" data-caption="">
                                            <img src="{{ asset('upload/basicdetails/' . $basic_data->image) }}" alt="image" style="height: 20px;width: 40px;">
                                        </a>
                    </div>
                    
                </div>
                <div class="col-md-6 pt-3" >
                    <label for="file_proof" class="form-label">FOA/SAP PO/Detailed WO:</label>
                    <input type="file" class="form-control" name="foa_sap_img" required>
                </div>

         
                <div class="col-md-12 pt-5">
                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#logem_img').change(function() {
            if ($(this).val() == "1") {
                $('#lo_gem_img').show();
            } else {
                $('#lo_gem_img').hide();
            }
        });
    });
</script>
