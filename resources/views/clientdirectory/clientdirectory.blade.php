@extends('layouts.app')
@section('page-title', 'Client Directory')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('clientdirectoryadd') }}" class="btn btn-primary btn-sm">Add Client Details</a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Organization</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Phone No</th>
                                        <th>EmailId</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clientdirectory as $key => $clientdirectoryData)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $clientdirectoryData->organization }}</td>
                                            <td>{{ $clientdirectoryData->name }}</td>
                                            <td>{{ $clientdirectoryData->designation }}</td>
                                            <td>{{ $clientdirectoryData->phone_no }}</td>
                                            <td>{{ $clientdirectoryData->email }}</td>
                                            <td>
                                                <a href="{{ asset('admin/clientdirectoryupdate/' . Crypt::encrypt($clientdirectoryData->id)) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/clientdirectorydelete/' . Crypt::encrypt($clientdirectoryData->id)) }}"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
@endsection
