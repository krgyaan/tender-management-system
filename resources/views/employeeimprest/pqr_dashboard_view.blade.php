@extends('layouts.app')
@section('page-title', 'PQR Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('pqr_dashboard_add') }}" class="btn btn-primary btn-sm">Add PQR</a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="ac-tab" data-bs-toggle="tab" href="#ac" role="tab"
                                    aria-controls="ac" aria-selected="true">AC Dashboard</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="dc-tab" data-bs-toggle="tab" href="#dc" role="tab"
                                    aria-controls="dc" aria-selected="false">DC Dashboard</a>
                            </li>
                        </ul>
                        <!-- Tab Content -->
                        <div class="tab-content" id="myTabContent">
                            <!-- AC Tab Content -->
                            <div class="tab-pane fade show active" id="ac" role="tabpanel" aria-labelledby="ac-tab">
                                <div class="table-responsive">
                                    <table class="table" id="allUsers">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr.No.</th>
                                                <th>Project Name</th>
                                                <th>Value</th>
                                                <th>PO date</th>
                                                <th>Upload PO</th>
                                                <th>Completion date</th>
                                                <th>Upload Completion</th>
                                                <th>Upload Performance <br>Certificate</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($ac_data && count($ac_data) > 0)
                                                @foreach ($ac_data as $key => $acitem)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $acitem->project_name }}</td>
                                                        <td>{{ format_inr($acitem->value) }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($acitem->po_date)) }}</td>
                                                        <td>
                                                            @if ($acitem->uplode_po)
                                                                @php
                                                                    $imageNames = json_decode($acitem->uplode_po, true);
                                                                    if (!$imageNames) {
                                                                        $imageNames = explode(',', $acitem->uplode_po);
                                                                    }
                                                                @endphp

                                                                @foreach ($imageNames as $imageName)
                                                                    <a href="{{ asset('uploads/pqr/' . $imageName) }}"
                                                                        target="_blank" rel="noopener noreferrer">
                                                                        Image {{ $loop->iteration }}
                                                                    </a>
                                                                @endforeach
                                                            @endif
                                                        <td>{{ date('d/m/Y', strtotime($acitem->completion_date)) }}</td>
                                                        <td>
                                                            <a href="{{ asset('uploads/pqr/' . $acitem->uplode_completion) }}"
                                                                target="_blank" rel="noopener noreferrer">
                                                                View
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('uploads/pqr/' . $acitem->performace_cretificate) }}"
                                                                target="_blank" rel="noopener noreferrer">
                                                                View
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                                data-bs-target="#acexampleModal"
                                                                class="btn btn-warning btn-xs"
                                                                onClick="showacdata('{{ $acitem->id }}', '{{ $acitem->team_name }}', '{{ $acitem->sap_gem_po_date }}', '{{ $acitem->remarks }}', '{{ asset('uploads/pqr/' . $acitem->uplode_sap_gem_po) }}');">View</a>

                                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                                data-bs-target="#acuplode" class="btn btn-warning btn-xs d-none"
                                                                onClick="acupdate('{{ $acitem->id }}');">Upload
                                                                PQ</a>
                                                            <a href="{{ asset('admin/pqr_edit/' . Crypt::encrypt($acitem->id)) }}"
                                                                class="btn btn-info btn-xs">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                            <a onclick="return check_delete()"
                                                                href="{{ asset('admin/pqr_delete/' . Crypt::encrypt($acitem->id)) }}"
                                                                class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                            <form action="https://abs.hyperofficial.in/admin/user/delete/3"
                                                                method="POST" id="deleteForm3" style="display: none;">
                                                                <input type="hidden" name="_token"
                                                                    value="jGhSwqCSanR436MmrNYCNMheJC3vdqy4eIDr1MLI"
                                                                    autocomplete="off">
                                                                <input type="hidden" name="_method" value="POST">
                                                                <input type="hidden" name="id" value="3">
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- DC Tab Content -->
                            <div class="tab-pane fade" id="dc" role="tabpanel" aria-labelledby="dc-tab">
                                <div class="table-responsive">
                                    <table class="table" id="allUsers">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr.No.</th>
                                                <th>Project Name</th>
                                                <th>Value</th>
                                                <th>PO date</th>
                                                <th>Upload PO</th>
                                                <th>Completion date</th>
                                                <th>Upload Completion</th>
                                                <th>Upload Performance <br> Certificate</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($dc_data))
                                                @foreach ($dc_data as $key => $pqritem)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $pqritem->project_name }}</td>
                                                        <td>{{ format_inr($pqritem->value) }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($pqritem->po_date)) }}</td>
                                                        <td>
                                                            @if ($pqritem->uplode_po)
                                                                @php
                                                                    $imageNames = json_decode($pqritem->uplode_po, true) ?? explode(',', $pqritem->uplode_po);
                                                                @endphp

                                                                @foreach ($imageNames as $imageName)
                                                                    <a href="{{ asset('uploads/pqr/' . $imageName) }}"
                                                                        target="_blank" rel="noopener noreferrer">
                                                                        Image {{ $loop->iteration }}
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d/m/Y', strtotime($pqritem->completion_date)) }}</td>
                                                        <td>
                                                            <a href="{{ asset('uploads/pqr/' . $pqritem->uplode_completion) }}"
                                                                target="_blank" rel="noopener noreferrer">
                                                                View
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('uploads/pqr/' . $pqritem->performace_cretificate) }}"
                                                                target="_blank" rel="noopener noreferrer">
                                                                View
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal"
                                                                class="btn btn-warning btn-xs"
                                                                onClick="showdcdata('{{ $pqritem->id }}', '{{ $pqritem->team_name }}', '{{ $pqritem->sap_gem_po_date }}', '{{ $pqritem->remarks }}', '{{ asset('uploads/pqr/' . $pqritem->uplode_sap_gem_po) }}');">View</a>
                                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                                data-bs-target="#uploadPQ" class="btn btn-warning btn-xs d-none"
                                                                onClick="update('{{ $pqritem->id }}');">Upload PQ</a>


                                                            <a href="{{ asset('admin/pqr_edit/' . Crypt::encrypt($pqritem->id)) }}"
                                                                class="btn btn-info btn-xs">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                            <a onclick="return check_delete()"
                                                                href="{{ asset('admin/pqr_delete/' . Crypt::encrypt($pqritem->id)) }}"
                                                                class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                            <form action="https://abs.hyperofficial.in/admin/user/delete/3"
                                                                method="POST" id="deleteForm3" style="display: none;">
                                                                <input type="hidden" name="_token"
                                                                    value="jGhSwqCSanR436MmrNYCNMheJC3vdqy4eIDr1MLI"
                                                                    autocomplete="off">
                                                                <input type="hidden" name="_method" value="POST">
                                                                <input type="hidden" name="id" value="3">
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="acuplode" tabindex="-1" aria-labelledby="uploadPQLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadPQLabel">Upload PQ Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadPQForm" action="{{ asset('admin/ac_upload_po') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="ac_id" value="{{ !empty($acitem) && $acitem->id }}">
                            <div class="mb-3">
                                <label for="upload_po" class="form-label">Select Images</label>
                                <input type="file" class="form-control" name="upload_po[]" id="upload_po" multiple
                                    required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uploadPQ" tabindex="-1" aria-labelledby="uploadPQLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadPQLabel">Upload PQ Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadPQForm" action="{{ asset('admin/upload_po') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pq_id" value="{{ !empty($pqritem) && $pqritem->id }}">
                            <div class="mb-3">
                                <label for="upload_po" class="form-label">Select Images</label>
                                <input type="file" class="form-control" name="upload_po[]" id="upload_po" multiple
                                    required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Dc Dashboard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control" name="dcid" id="dcid">
                            <p class="col-md-4" name="dcteam_name">
                                <strong>Team Name:</strong> <span id="dcteam_name"></span>
                            </p>
                            <p class="col-md-8" name="dcsap_gem_po_date">
                                <strong>SAP/GEM PO date:</strong> <span id="dcsap_gem_po_date"></span>
                            </p>
                            <p class="col-md-12"name="dcremarks">
                                <strong>Remarks:</strong> <span id="dcremarks"></span>
                            </p>
                            <p class="col-md-12" name="dcupload_sap_gem_po">
                                <strong>Upload SAP/GEM PO:</strong>
                            </p>
                            <img id="dcupload_sap_gem_po" src="" width="50%">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="acexampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ac Dashboard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control" name="id" id="id">

                            <p class="col-md-4" name="team_name"><strong>Team Name:</strong> <span id="team_name"></span>
                            </p>

                            <p class="col-md-8" name="sap_gem_po_date"><strong>SAP/GEM PO date:</strong> <span
                                    id="sap_gem_po_date"></span></p>
                            <p class="col-md-12"name="remarks"><strong>Remarks:</strong> <span id="remarks"></span></p>
                            <p class="col-md-12" name="upload_sap_gem_po"><strong>Upload SAP/GEM PO:</strong> </p>
                            <img id="upload_sap_gem_po" src="" width="50%">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        function showacdata(id, team_name, sap_gem_po_date, remarks, upload_sap_gem_po) {
            $('#team_name').html(team_name);
            $('#sap_gem_po_date').html(sap_gem_po_date);
            $('#remarks').html(remarks);

            $('#upload_sap_gem_po').attr('src', upload_sap_gem_po).css('width', '50%');
            $('#id').val(id);

        }

        function showdcdata(id, team_name, sap_gem_po_date, remarks, upload_sap_gem_po) {
            $('#dcteam_name').html(team_name);
            $('#dcsap_gem_po_date').html(sap_gem_po_date);
            $('#dcremarks').html(remarks);
            $('#dcupload_sap_gem_po').attr('src', upload_sap_gem_po).css('width', '50%');
            $('#dcid').val(id);
        }
    </script>
@endpush
