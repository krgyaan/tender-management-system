@extends('layouts.app')
@section('page-title', 'WO Update')
@section('content')
    <scetion>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ asset('/admin/woupdate_post') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input type="hidden" value="{{ $basic_data->id }}" name="id">
                                <div class="col-md-4 pt-3">
                                    <label for="logem_img" class="form-label">PO Amendment Needed</label>
                                    <select name="lo_gem_loi" class="form-control" id="logem_img" required>
                                        <option value="" disabled selected>Select</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div id="lo_gem_img" class="col-md-4 pt-3" style="display: none;">
                                    <label for="file_proof" class="form-label">LO/GEM/LOI/Draft WO:</label>
                                    <input type="file" class="form-control" name="lo_gem_img">
                                </div>
                                <div class="col-md-4 pt-3">
                                    <label for="file_proof" class="form-label">FOA/SAP PO/Detailed WO:</label>
                                    <input type="file" class="form-control" name="foa_sap_img" required>
                                </div>
                                <div class="col-md-12 pt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </scetion>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#logem_img').change(function() {
                if ($(this).val() == 1) {
                    $('#lo_gem_img').show();
                } else {
                    $('#lo_gem_img').hide();
                }
            });
        });
    </script>
@endpush
