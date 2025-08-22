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
                                        @foreach (['Daily', 'Weekly', 'Monthly'] as $f)
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

                                <div class="col-md-4 mt-3" id="weekly-days-field" style="display:none;">
                                    <label class="form-label">Select Weekdays</label>
                                    <select class="form-select @error('frequency_condition') is-invalid @enderror" name="frequency_condition">
                                        @foreach ([0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'] as $num => $day)
                                            <option value="{{ $num }}" {{ old('frequency_condition') !== null && old('frequency_condition') == $num ? 'selected' : '' }}>{{ $day }}</option>
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
                                            <option value="{{ $i }}" {{ old('frequency_condition') !== null && old('frequency_condition') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('frequency_condition')
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
        console.log(freqSelect);

        freqSelect.addEventListener('change', toggleFrequencyFields);
        toggleFrequencyFields(); // initial
    });
</script>
@endpush
