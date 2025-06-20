@extends('layouts.app')
@section('page-title', 'Create Account Checklist')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form method="POST" action="{{ route('checklists.store') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="task_name" class="form-label">Task Name </label>
                                    <input type="text" class="form-control @error('task_name') is-invalid @enderror"
                                        name="task_name" value="{{ old('task_name') }}" required>
                                    @error('task_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="description" class="form-label">Task Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-3">
                                    <label for="frequency" class="form-label">Frequency </label>
                                    <select class="form-control @error('frequency') is-invalid @enderror" name="frequency"
                                        required>
                                        <option value="">Choose Frequency</option>
                                        @foreach (['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annual'] as $f)
                                            <option value="{{ $f }}"
                                                {{ old('frequency') == $f ? 'selected' : '' }}>
                                                {{ $f }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label for="responsibility" class="form-label">Responsibility </label>
                                    <select class="form-control @error('responsibility') is-invalid @enderror"
                                        name="responsibility" required>
                                        <option value="">Select Responsible User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('responsibility') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsibility')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-3">
                                    <label for="accountability" class="form-label">Accountability </label>
                                    <select class="form-control @error('accountability') is-invalid @enderror"
                                        name="accountability" required>
                                        <option value="">Select Accountable User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('accountability') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('accountability')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary" type="submit">Save Task</button>
                                <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
