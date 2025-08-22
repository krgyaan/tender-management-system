@extends('layouts.app')
@section('page-title', 'WO Contract Agreement')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>WO Date</th>
                                        <th>Project Name</th>
                                        <th>WO Number</th>
                                        <th>Timer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($basic as $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->tenderName->tender_name }}</td>
                                            <td>{{ $data->number }}</td>
                                            <td></td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('viewbuttencontract', ['id' => $data->id]) }}"
                                                        class="btn btn-info btn-xs">View</a>
                                                    <a href="#" class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                        data-bs-target="#contractAgreementModal"
                                                        onclick="setcontractId({{ $data->id }})">
                                                        Upload Contract Agreement
                                                    </a>
                                                    <a href="#" class="btn btn-secondary btn-xs"
                                                        data-bs-toggle="modal" data-bs-target="#clientSignedModal"
                                                        onclick="setsignedId({{ $data->id }})">
                                                        Client Signed Document
                                                    </a>
                                                </div>
                                            </td>
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

    <script>
        function setcontractId(id) {
            document.getElementById('contract').value = id;
        }

        function setsignedId(id) {
            document.getElementById('signed').value = id;
        }
    </script>

    <!-- Modal for Contract Agreement -->
    <div class="modal fade" id="contractAgreementModal" tabindex="-1" aria-labelledby="contractAgreementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ asset('admin/uplade_contract_agereement') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="contract" value="">
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
    <div class="modal fade" id="clientSignedModal" tabindex="-1" aria-labelledby="clientSignedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ asset('admin/uplade_contract_agereement') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="signed" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientSignedModalLabel">Upload Client Signed Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" class="form-control" name="client_signed" />
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
