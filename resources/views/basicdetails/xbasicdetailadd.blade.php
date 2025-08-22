@extends('layouts.app')
@section('page-title', 'Basic Details Add')
@section('content')
<div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body p-4">
                            <form method="post" action="{{asset('/admin/basicdetailaddpost')}}" enctype="multipart/form-data"
                                  id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                                @csrf
                               <div class="col-md-4">
                                    <label for="name_id" class="form-label">Tender Name<span class="text-danger">*</span></label>
                                    <select name="tender_name_id" class="form-control" id="name_id" required>
                                        <option value="" selected disabled>Select Tender Name</option>
                                        @foreach ($tendername as $key => $data)
                                            <option value="{{ $data->id }}">{{ $data->tender_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('tender_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


    							<div class="col-md-4">
    								<label for="input28" class="form-label">WO Number</label>
    								<div class="input-group">
    									
    									<input type="text" class="form-control" name="number" id="input28" placeholder="Number." maxlength="10" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required>
    								  </div>
    							</div>
    							<div class="col-md-4">
    								<label for="input28" class="form-label">WO Date</label>
    								<div class="input-group">
    									
    									<input type="date" class="form-control" name="date" id="input28" placeholder="Date" required>
    								  </div>
    							</div>
    							<div class="col-md-4">
    								<label for="input28" class="form-label">WO Value(Pre-GST)</label>
    								<div class="input-group">
    									
    									<input type="text" class="form-control" name="pre_gst" id="input28" placeholder="Pre-GST" required>
    								  </div>
    							</div>
    							<div class="col-md-4">
    								<label for="input28" class="form-label">WO Value(GST Amt.)</label>
    								<div class="input-group">
    									
    									<input type="text" class="form-control" name="pre_amt" id="input28" placeholder="Pre Amt." required>
    								  </div>
    							</div>
    							<div class="col-md-4">
    								<label for="input28" class="form-label">LOA/GEM PO/LOI/Draft WO</label>
    								<div class="input-group">
    									
    									<input type="file" class="form-control" name="image" id="input28" placeholder="image" required>
    								  </div>
    							</div>
    							
    						
        					
                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection