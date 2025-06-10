@extends('layouts.app')
@section('page-title', ' Add PQR Dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('pqr_dashboard') }}" class="btn btn-outline-danger btn-sm">back</a>
                    </div>
                    <div class="card">
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('/admin/pqr_dashboard_post') }}"
                                enctype="multipart/form-data" id="formatDistrict-update" class="row g-3 needs-validation"
                                novalidate>
                                @csrf
                                <div class="col-md-4">
                                    <label for="" class="form-label">Team Name<span
                                            class="text-danger">*</span></label>
                                    <select name="team_name" class="form-control" required>
                                        <option value="">Select Option </option>
                                        <option value="AC">AC </option>
                                        <option value="DC">DC</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="project_name" class="form-label">Project Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="project_name" class="form-control" id=""
                                        placeholder="Project Name" required>

                                </div>
                                <div class="col-md-4">
                                    <label for="value" class="form-label">Value<span class="text-danger">*</span></label>
                                    <input type="text" name="value" class="form-control" id=""
                                        placeholder="Value" required>

                                </div>
                                <div class="col-md-4">
                                    <label for="item" class="form-label">Item<span class="text-danger">*</span></label>
                                    <input type="text" name="item" class="form-control" id=""
                                        placeholder="Item" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="po_date" class="form-label">PO date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="po_date" class="form-control" id=""
                                        placeholder="PO date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="uplode_po" class="form-label">Upload PO<span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="uplode_po" class="form-control" id=""
                                        placeholder="Upload PO" required>

                                </div>
                                <div class="col-md-4">
                                    <label for="sap_gem_po_date" class="form-label">SAP/GEM PO date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="sap_gem_po_date" class="form-control" id=""
                                        placeholder="SAP/GEM PO date" required>

                                </div>
                                <div class="col-md-4">
                                    <label for="uplode_sap_gem_po" class="form-label">Upload SAP/GEM PO<span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="uplode_sap_gem_po" class="form-control" id=""
                                        placeholder="Upload SAP/GEM PO" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="completion_date" class="form-label">Completion date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="completion_date" class="form-control" id=""
                                        placeholder="Completion date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="uplode_completion" class="form-label">Upload Completion<span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="uplode_completion" class="form-control" id=""
                                        placeholder="Upload Completion" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="performace_cretificate" class="form-label">Upload Performace
                                        Certificate<span class="text-danger">*</span></label>
                                    <input type="file" name="performace_cretificate" class="form-control"
                                        id="" placeholder="Upload Performace Certificate" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="remarks" class="form-label">Remarks <span
                                            class="text-danger">*</span></label>
                                    <textarea name="remarks" id="" cols="80" rows="4" class="form-control"></textarea>
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
