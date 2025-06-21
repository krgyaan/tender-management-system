@extends('layouts.app')

@section('page-title', 'Edit Account Checklist')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class=" bg-primary text-white">
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('checklists.update', $checklist->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="task_name" class="form-label">Task Name </label>
                                    <input type="text" class="form-control @error('task_name') is-invalid @enderror"
                                        name="task_name" value="{{ old('task_name', $checklist->task_name) }}" required>
                                    @error('task_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="frequency" class="form-label">Frequency </label>
                                    <select class="form-control @error('frequency') is-invalid @enderror" name="frequency"
                                        required>
                                        <option value="">Select Frequency</option>
                                        <option value="Daily"
                                            {{ old('frequency', $checklist->frequency) == 'Daily' ? 'selected' : '' }}>Daily
                                        </option>
                                        <option value="Weekly"
                                            {{ old('frequency', $checklist->frequency) == 'Weekly' ? 'selected' : '' }}>
                                            Weekly</option>
                                        <option value="Monthly"
                                            {{ old('frequency', $checklist->frequency) == 'Monthly' ? 'selected' : '' }}>
                                            Monthly</option>
                                        <option value="Yearly"
                                            {{ old('frequency', $checklist->frequency) == 'Yearly' ? 'selected' : '' }}>
                                            Yearly</option>
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $checklist->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="responsibility" class="form-label">Responsibility User</label>
                                    <select class="form-control" name="responsibility">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('responsibility', $checklist->responsibility) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="accountability" class="form-label">Accountability User</label>
                                    <select class="form-control" name="accountability">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('accountability', $checklist->accountability) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Update Checklist</button>
                                <a href="{{ route('checklists.show', $checklist->id) }}"
                                    class="btn btn-secondary ms-2">Cancel</a>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
