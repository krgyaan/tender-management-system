@extends('layouts.app')
@section('page-title', 'WO Contract Agreement')
@section('content')
<div class="container-fluid content-inner p-0">
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <!--<a href="" class="btn btn-primary">WO Detail Add</a>-->
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>WO Date</th>
                                        <th>Project Name</th>
                                        <th>WO Number</th>
                                        <th>Action</th>
                                        <th>Timer</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wo_data as $key => $row)
                                        @php
                                           $matchedData = $basic_data->where('id', $row->basic_detail_id)->first();
                                        $tenderdata = $tendername->where('id', optional($matchedData)->tender_name_id)->first();

                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $matchedData ? \Carbon\Carbon::parse($matchedData->date)->format('d-m-Y') : '' }}</td>
                                            <td>{{ $tenderdata ? Str::limit($tenderdata->tender_name, 20) : 'N/A' }}</td>
                                            <td> {{ $matchedData ? $matchedData->number : '' }}</td>
                                            <td>
                                               
                                                <a  href="{{ asset('admin/viewbuttencontract/' . Crypt::encrypt($row->id)) }}"  class="btn btn-warning btn-sm btn-icon">View Button</a>
                                                <a href="#" class="btn btn-warning btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#contractAgreementModal" onclick="setcontractId({{ $row->id }})" >Contract Agreement</a><br>
                                                <a href="#" class="mt-1 btn btn-warning btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#clientSignedModal" onclick="setsignedId({{ $row->id }})"  >Client Signed</a>
                                               
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
  function setcontractId(id) {
    document.getElementById('contract').value = id;
  }
  function setsignedId(id) {
    document.getElementById('signed').value = id;
  }
</script>


<!-- Modal for Contract Agreement -->
<div class="modal fade" id="contractAgreementModal" tabindex="-1" aria-labelledby="contractAgreementModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="{{asset('admin/uplade_contract_agereement')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="contract" value="" >
              <div class="modal-header">
                <h5 class="modal-title" id="contractAgreementModalLabel">Upload Contract Agreement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="file" class="form-control" name="contract_agreement" />
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Upload</button>
              </div>
        </form>
    </div>
  </div>
</div>



<!-- Modal for Client Signed -->
<div class="modal fade" id="clientSignedModal" tabindex="-1" aria-labelledby="clientSignedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="{{asset('admin/uplade_contract_agereement')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="signed" value="" >
          <div class="modal-header">
            <h5 class="modal-title" id="clientSignedModalLabel">Upload Client Signed Document</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="file" class="form-control" name="client_signed"  />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
      </form>
    </div>
  </div>
</div>





@endsection