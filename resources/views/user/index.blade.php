@extends('layouts.app')
@section('page-title', 'All Employees Info')
@section('content')
@php use Illuminate\Support\Str; @endphp
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin/user/create') }}" class="btn btn-primary">Create New User</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="table-responsive">
                            <table class="table" id="allUsers">
                                <thead class="">
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Designation</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users->sortBy('name') as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                {{ Str::of($user->role)->replace('-', ' ')->title() }}
                                            </td>
                                            <td>
                                                {{ Str::of($user->designation)->replace('-', ' ')->title()->replace('Dc', 'DC')->replace('Ac ', 'AC ')->replace('Ceo', 'CEO')->replace('Coo', 'COO') }}
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->mobile }}</td>
                                            <td>
                                                @if ($user->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin/user/edit', $user->id) }}"
                                                    class="btn btn-info btn-xs">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="#"
                                                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $user->id }}').submit();"
                                                    class="btn btn-danger btn-xs">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                <form action="{{ route('admin/user/delete', $user->id) }}" method="POST"
                                                    id="deleteForm{{ $user->id }}" style="display: none;">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                                </form>
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
