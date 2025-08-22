@extends('layouts.app')
@section('page-title', 'Kick Off Meeting Dashboard')
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
                                            <td>{{ $data->tenderName?->tender_name }}</td>
                                            <td>{{ $data->number }}</td>
                                            <td></td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('viewbutten_dashboard', ['id' => $data->id]) }}"
                                                        class="btn btn-info btn-xs">View</a>
                                                    @if ($data->wo_details)
                                                        <a href="{{ route('initiate_meeting', ['id' => $data->wo_details->id]) }}"
                                                            class="btn btn-secondary btn-xs">
                                                            Initiate Meeting
                                                        </a>
                                                    @endif
                                                    <a href="#" class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                        data-bs-target="#uploadMOMModal"
                                                        onclick="setMomId({{ $data->id }})">
                                                        Upload MOM
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
        function setMomId(id) {
            document.getElementById('momId').value = id;
        }
    </script>

    <div class="modal fade" id="uploadMOMModal" tabindex="-1" aria-labelledby="uploadMOMModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ asset('admin/uplode_mom') }}" method="POST" enctype="multipart/form-data"
                    class="row g-3 needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="momId" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadMOMModalLabel">Upload MOM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" class="form-control" name="uplode_mom" required />
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
