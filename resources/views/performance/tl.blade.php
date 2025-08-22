@extends('layouts.app')
@section('page-title', 'Team Leader Performance')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="new-user-info">
                    <form method="POST" action="" id="performanceForm">
                        @csrf

                        @php
                            $team_member ??= $_POST['team_member'] ?? $defaultTeamMember ?? '';
                            $performance_mode ??= $_POST['performance_mode'] ?? 'team-leader';
                        @endphp

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label" for="address">Team Member:</label>
                                <select name="team_member" class="form-control" id="team_member" required>
                                    <option value="">Select Team Member</option>
                                    @foreach ($users as $user)
                                        <option {{ $team_member == $user->id ? 'selected' : '' }} value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->team }})
                                        </option>
                                    @endforeach
                                </select>
                                <small>
                                    <span class="text-danger">{{ $errors->first('team_member') }}</span>
                                </small>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="from_date">From Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="from_date" class="form-control" id="from_date"
                                            value="{{ old('from_date') ?? ($_POST['from_date'] ?? '') }}">
                                    </div>
                                    <small>
                                        <span class="text-danger">{{ $errors->first('from_date') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="to_date">To Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="to_date" class="form-control" id="to_date"
                                            value="{{ old('to_date') ?? ($_POST['to_date'] ?? '') }}">
                                    </div>
                                    <small>
                                        <span class="text-danger">{{ $errors->first('to_date') }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Mode Toggle -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Performance Mode:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="performance_mode"
                                        id="team-leader-mode" value="team-leader" {{ $performance_mode === 'team-leader' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="team-leader-mode">Team Leader Only</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="performance_mode" id="team-mode"
                                        value="team" {{ $performance_mode === 'team' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="team-mode">Entire Team</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" id="submit" name="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @if ($result)
                <div class="row">
                    @php
                        $totalEmdAmount = array_sum(array_column($tenderWithEmd, 'emds'));
                        $totalEmdBack = array_sum(array_column($tenderWithEmd, 'emdback'));
                    @endphp

                    <div class="col-md-12">
                        <p class="my-4">
                            You have submitted bid for <span class="fw-bold">{{ $totalTenderCount }}</span> tenders,
                            With a total of <span class="fw-bold">{{ $doableStages ?? 0 }}</span> steps per tender,
                            you have successfully completed
                            <span class="fw-bold">{{ $overallSummary['done'] ?? 0 }}</span> steps out of
                            <span class="fw-bold">{{ $overallSummary['tenderhave'] ?? 0 }}</span>, with
                            <span class="fw-bold">{{ $overallSummary['ontime'] ?? 0 }}</span> steps completed on schedule.
                        </p>
                    </div>

                    {{-- Work Done --}}
                    <div class="col-lg-3">
                        <div class="card shining-card">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">
                                    Work Not Done
                                </a>
                                <div class="progress-detail pt-3">
                                    <h4 class="counter text-danger" style="visibility: visible;">
                                        @php
                                            $tenderhave = $overallSummary['tenderhave'] ?? 0;
                                            $done = $overallSummary['done'] ?? 0;
                                            $percentage = $tenderhave ? ($done / $tenderhave) * 100 : 0;
                                            $notDonePer = $percentage > 0 ? 100 - $percentage : 0;
                                        @endphp
                                        {{ number_format($notDonePer, 2) }}%
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Work Done On Time --}}
                    <div class="col-lg-3">
                        <div class="card shining-card">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">Not Done On Time</a>
                                <div class="progress-detail pt-3">
                                    <h4 class="counter text-danger" style="visibility: visible;">
                                        @php
                                            $done = $overallSummary['done'] ?? 0;
                                            $ontime = $overallSummary['ontime'] ?? 0;
                                            $percentage = $done ? ($ontime / $done) * 100 : 0;
                                            $notOntimePer = $percentage > 0 ? 100 - $percentage : 0;
                                        @endphp
                                        {{ number_format($notOntimePer, 2) }}%
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EMD PAID --}}
                    <div class="col-lg-3">
                        <div class="card shining-card">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">EMD Paid</a>
                                <div class="progress-detail pt-3">
                                    <h4 class="counter text-success" style="visibility: visible;">
                                        {{ format_inr($totalEmdAmount) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EMD BACK --}}
                    <div class="col-lg-3">
                        <div class="card shining-card">
                            <div class="card-body">
                                <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">EMD Received back</a>
                                <div class="progress-detail pt-3">
                                    <h4 class="counter text-success" style="visibility: visible;">
                                        {{ format_inr($totalEmdBack) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tender Count & Their Sum Amount --}}
                    @foreach ($tenderStatusCounts as $tender => $value)
                        <div class="col-lg-3">
                            <div class="card shining-card">
                                <div class="card-body">
                                    <a href="javascript:void(0);" class="stretched-link fw-bold fs-5 me-2">
                                        {{ Str::title(str_replace('_', ' ', $tender)) }}
                                    </a>
                                    <div class="progress-detail pt-3">
                                        <h5 class="counter text-success" style="visibility: visible;">Count:
                                            {{ $value['count'] }}
                                        </h5>
                                        <h4 class="counter text-success" style="visibility: visible;">
                                            {{ format_inr($value['value']) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div>
                    @if (isset($performanceData))
                        <div class="overall-summary mb-5">
                            <h3 class="mb-3">Overall Performance Summary</h3>
                            <table class="table-bordered w-50">
                                <tr>
                                    <th class="p-2">Total Tenders Handled</th>
                                    <td class="p-2">{{ $performanceData['overall_metrics']['total_tenders'] }}</td>
                                </tr>
                                <tr>
                                    <th class="p-2">Overall Completion Rate</th>
                                    <td class="p-2">
                                        {{ $performanceData['overall_metrics']['overall_completion_rate'] }}%
                                    </td>
                                </tr>
                                <tr>
                                    <th class="p-2">On-time Completion Rate</th>
                                    <td class="p-2">{{ $performanceData['overall_metrics']['overall_on_time_rate'] }}%
                                    </td>
                                </tr>
                                <tr>
                                    <th class="p-2">Total EMD Submitted</th>
                                    <td class="p-2">
                                        {{ number_format($performanceData['overall_metrics']['total_emd_submitted'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="p-2">Total EMD Returned</th>
                                    <td class="p-2">
                                        {{ number_format($performanceData['overall_metrics']['total_emd_returned'], 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="tender-wise-details">
                            <h3 class="mb-4">Tender-wise Details</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="">
                                        <tr>
                                            <th>Tender #</th>
                                            <th>Stages Completed</th>
                                            <th>Work</th>
                                            <th>Stage Timeline</th>
                                            <th>EMD Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($performanceData['tender_wise_details'] as $tenderId => $tenderData)
                                            <tr>
                                                <td @style(['min-width: 200px'])>
                                                    <p class="mb-0 fw-bold">
                                                        {{ $tenderData['tender_info']['tender_name'] }}</p>
                                                    {{ $tenderData['tender_info']['tender_no'] }}
                                                </td>
                                                <td>
                                                    <p>
                                                        {{ $tenderData['stages_info']['completed_stages'] }} /
                                                        {{ $tenderData['stages_info']['total_stages'] }}
                                                    </p>
                                                    <p>{{ $tenderData['stages_info']['completion_percentage'] }}%</p>
                                                </td>
                                                <td>
                                                    Total: {{ $tenderData['stages_info']['total_stages'] }}<br>
                                                    Done: {{ $tenderData['stages_info']['completed_stages'] }}<br>
                                                    On Time: {{ $tenderData['stages_info']['completed_on_time'] }}
                                                </td>
                                                <td>
                                                    @foreach ($tenderData['stage_timelines'] as $timeline)
                                                        <span style="font-size: x-small;" @class([
                                                            'badge me-1',
                                                            'bg-success' => $timeline['completed'] && !$timeline['on_time'],
                                                            'bg-info text-white' => $timeline['completed'] && $timeline['on_time'],
                                                            'bg-warning text-dark' => $timeline['skipped'],
                                                            'bg-light text-dark' => !$timeline['completed'] && !$timeline['skipped'],
                                                        ])>
                                                            {{ $timeline['stage'] }}
                                                            @if ($timeline['on_time'])
                                                                <i class="fa fa-clock"></i>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    Paid: ₹{{ format_inr($tenderData['emd_info']['submitted_amount']) }}
                                                    <br>
                                                    Return: ₹{{ format_inr($tenderData['emd_info']['returned_amount']) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle performance mode toggle
            const performanceModeRadios = document.querySelectorAll('input[name="performance_mode"]');
            const form = document.getElementById('performanceForm');

            performanceModeRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    // Auto-submit form when performance mode changes
                    if (form.checkValidity()) {
                        form.submit();
                    }
                });
            });

            // Handle team member selection change
            const teamMemberSelect = document.getElementById('team_member');
            teamMemberSelect.addEventListener('change', function () {
                // Auto-submit form when team member changes
                if (form.checkValidity()) {
                    form.submit();
                }
            });

            // Handle date changes
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.addEventListener('change', function () {
                    // Auto-submit form when dates change
                    if (form.checkValidity()) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
