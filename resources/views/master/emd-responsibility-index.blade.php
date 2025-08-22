@extends('layouts.app')
@section('page-title', 'EMD Responsibility Management')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        {{-- Form Section --}}
                        <div class="mb-5">
                            @if(isset($editMode) && $editMode && isset($responsibility))
                                <form method="POST" action="{{ route('emd-responsibility.update', $responsibility->id) }}">
                                    @csrf
                                    @method('PUT')
                            @else
                                    <form method="POST" action="{{ route('emd-responsibility.store') }}">
                                        @csrf
                                @endif
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group mb-2">
                                                <label for="instrument_type">Instrument Type</label>
                                                <select name="instrument_type" id="instrument_type" class="form-control"
                                                    required>
                                                    <option value="">Select Instrument Type</option>
                                                    @foreach($instrumentTypes as $key => $type)
                                                        <option value="{{ $key }}" {{ (isset($responsibility) && $responsibility->responsible_for == $key) ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group mb-2">
                                                <label for="user_id">User</label>
                                                <select name="user_id" id="user_id" class="form-control" required>
                                                    <option value="">Select User</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ (isset($responsibility) && $responsibility->user_id == $user->id) ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-group mb-2">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ (isset($editMode) && $editMode) ? 'Update' : 'Assign' }}
                                                </button>
                                                @if(isset($editMode) && $editMode)
                                                    <a href="{{ route('emd-responsibility.index') }}"
                                                        class="btn btn-secondary">Cancel</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                        </div>
                        {{-- List Section --}}
                        <table class="table table-bordered mt-5">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Instrument Type</th>
                                    <th>Responsible User</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($responsibilities as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $instrumentTypes[$item->responsible_for] ?? $item->responsible_for }}</td>
                                        <td>{{ $item->responsible ? $item->responsible->name . ' (' . $item->responsible->email . ')' : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('emd-responsibility.index', ['edit' => $item->id]) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('emd-responsibility.destroy', $item->id) }}" method="POST"
                                                style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete this responsibility?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
