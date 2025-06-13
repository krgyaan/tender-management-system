@extends('layouts.app')
@section('page-title', 'Organization Industries')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            {{-- Add/Edit Form Section --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($editIndustry) ? 'Edit Industry' : 'Add New Industry' }}</h5>
                </div>
                <div class="card-body">
                    <form id="industryForm" method="POST"
                        action="{{ isset($editIndustry) ? route('org-industries.update', $editIndustry->id) : route('org-industries.add') }}">
                        @csrf
                        @if (isset($editIndustry))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Industry Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ isset($editIndustry) ? $editIndustry->name : '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="1">{{ isset($editIndustry) ? $editIndustry->description : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($editIndustry) ? 'Update' : 'Save' }} Industry
                            </button>
                            @if (isset($editIndustry))
                                <a href="{{ route('org-industries.add') }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Industries List Section --}}
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($industries as $industry)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $industry->name }}</td>
                                        <td>{{ $industry->description }}</td>
                                        <td>
                                            <span class="text-{{ $industry->status == '1' ? 'success' : 'danger' }}">
                                                {{ $industry->status == '1' ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <a href="{{ route('org-industries.edit', $industry->id) }}"
                                                class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('org-industries.delete', $industry->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-xs"
                                                    onclick="return confirm('Are you sure you want to delete this industry?')">
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
@endsection
