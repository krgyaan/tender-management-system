@extends('layouts.app')
@section('page-title', 'Item Headings')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            {{-- Add/Edit Form Section --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($editHeading) ? 'Edit Heading' : 'Add New Heading' }}</h5>
                </div>
                <div class="card-body">
                    <form id="headingForm" method="POST"
                        action="{{ isset($editHeading) ? route('headings.update', $editHeading->id) : route('items.add-heading') }}">
                        @csrf
                        @if (isset($editHeading))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="team">Team</label>
                                    <select class="form-control" id="team" name="team" required>
                                        <option value="" selected disabled>Select Team</option>
                                        <option value="DC"
                                            {{ isset($editHeading) && $editHeading->team == 'DC' ? 'selected' : '' }}>DC
                                        </option>
                                        <option value="AC"
                                            {{ isset($editHeading) && $editHeading->team == 'AC' ? 'selected' : '' }}>AC
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Heading Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ isset($editHeading) ? $editHeading->name : '' }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($editHeading) ? 'Update' : 'Save' }} Heading
                            </button>
                            @if (isset($editHeading))
                                <a href="{{ route('items.add-heading') }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Headings List Section --}}
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="bd-example">
                        <nav>
                            <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                <button class="nav-link {{ Auth::user()->team == 'AC' ? 'active' : '' }}" id="nav-ac-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-ac" type="button" role="tab"
                                    aria-controls="nav-ac" aria-selected="true">Team AC Headings</button>
                                <button class="nav-link {{ Auth::user()->team == 'DC' ? 'active' : '' }}" id="nav-dc-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-dc" type="button" role="tab"
                                    aria-controls="nav-dc" aria-selected="false">Team DC Headings</button>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade {{ Auth::user()->team == 'AC' ? 'show active' : '' }}" id="nav-ac"
                                role="tabpanel" aria-labelledby="nav-ac-tab">
                                <div class="table-responsive">
                                    <table class="table" id="ac-headings-table">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Name</th>
                                                <th>Team</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($acHeadings as $heading)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $heading->name }}</td>
                                                    <td>{{ $heading->team }}</td>
                                                    <td>
                                                        <span
                                                            class="text-{{ $heading->status == '1' ? 'success' : 'danger' }}">
                                                            {{ $heading->status == '1' ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td class="d-flex gap-1">
                                                        <a href="{{ route('headings.edit', $heading->id) }}"
                                                            class="btn btn-warning btn-xs">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('headings.delete', $heading->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade {{ Auth::user()->team == 'DC' ? 'show active' : '' }}" id="nav-dc"
                                role="tabpanel" aria-labelledby="nav-dc-tab">
                                <div class="table-responsive">
                                    <table class="table" id="dc-headings-table">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Name</th>
                                                <th>Team</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dcHeadings as $heading)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $heading->name }}</td>
                                                    <td>{{ $heading->team }}</td>
                                                    <td>
                                                        <span
                                                            class="text-{{ $heading->status == '1' ? 'success' : 'danger' }}">
                                                            {{ $heading->status == '1' ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td class="d-flex gap-1">
                                                        <a href="{{ route('headings.edit', $heading->id) }}"
                                                            class="btn btn-warning btn-xs">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('headings.delete', $heading->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
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
            </div>
        </div>
    </div>
@endsection
