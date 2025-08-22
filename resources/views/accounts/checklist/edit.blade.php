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
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 mt-3" id="weekly-days-field" style="display:none;">
                                    <label class="form-label">Select Weekdays</label>
                                    <select class="form-select @error('frequency_condition') is-invalid @enderror" name="frequency_condition">
                                        @foreach ([0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'] as $num => $day)
                                            <option value="{{ $num }}" {{ (string)old('frequency_condition', $checklist->frequency == 'Weekly' ? $checklist->frequency_condition : null) === (string)$num ? 'selected' : '' }}>{{ $day }}</option>
                                        @endforeach
                                    </select>
                                    @error('frequency_condition')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-3" id="monthly-day-field" style="display:none;">
                                    <label class="form-label">Day of Month</label>
                                    <select class="form-select @error('frequency_condition') is-invalid @enderror" name="frequency_condition">
                                        <option value="">Select Day</option>
                                        @for ($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}" {{ (string)old('frequency_condition', $checklist->frequency == 'Monthly' ? $checklist->frequency_condition : null) === (string)$i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('frequency_condition')
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
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleFrequencyFields() {
        var freq = document.querySelector('select[name="frequency"]').value;
        var weeklyField = document.getElementById('weekly-days-field');
        var monthlyField = document.getElementById('monthly-day-field');
        var weeklySelect = weeklyField.querySelector('select[name="frequency_condition"]');
        var monthlySelect = monthlyField.querySelector('select[name="frequency_condition"]');

        if (freq === 'Weekly') {
            weeklyField.style.display = '';
            weeklySelect.disabled = false;
            weeklySelect.setAttribute('required', 'required');
            monthlyField.style.display = 'none';
            monthlySelect.disabled = true;
            monthlySelect.removeAttribute('required');
        } else if (freq === 'Monthly') {
            monthlyField.style.display = '';
            monthlySelect.disabled = false;
            monthlySelect.setAttribute('required', 'required');
            weeklyField.style.display = 'none';
            weeklySelect.disabled = true;
            weeklySelect.removeAttribute('required');
        } else {
            weeklyField.style.display = 'none';
            weeklySelect.disabled = true;
            weeklySelect.removeAttribute('required');
            monthlyField.style.display = 'none';
            monthlySelect.disabled = true;
            monthlySelect.removeAttribute('required');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        var freqSelect = document.querySelector('select[name="frequency"]');
        freqSelect.addEventListener('change', toggleFrequencyFields);
        toggleFrequencyFields(); // initial
    });
</script>
@endpush
