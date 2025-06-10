@extends('layouts.app')
@section('page-title', 'Imprest Payment History')
@section('content')
    <section>
        <div class="row">
            @include('partials.messages')
            <div class="col-md-12 m-auto mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ URL::previous() }}" class="btn btn-outline-danger btn-sm">
                        Back
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table class="table dataTable" id="allUsers">
                                <thead>
                                    <tr>
                                        <td>Sr.No.</td>
                                        <td>Name</td>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Project Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $txn)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $txn->team_member_name }}</td>
                                            <td>{{ date('d-m-Y', $txn->strtotime) }}</td>
                                            <td>{{ $txn->amount }}</td>
                                            <td>{{ $txn->project_name }}</td>
                                            <td>
                                                @if ($txn->name_id == Auth::user()->id || in_array(Auth::user()->role, ['admin', 'account']))
                                                    <a href="{{ route('employeeimprest.edit-history', $txn->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('employeeimprest.delete-history', $txn->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
                                                @endif
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
