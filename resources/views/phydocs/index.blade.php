@extends('layouts.app')
@section('page-title', 'All Physical Documnents')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('phydocs.create') }}" class="btn btn-primary btn-sm">Courier Physical Documents</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Physical Docs Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">RFQ Sumnitted</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <table class="table" id="allUsers">
                                        <thead class="">
                                            <tr>
                                                <th>Tender No</th>
                                                <th>Tender Name</th>
                                                <th>Team Member</th>
                                                <th>Due Date/Time</th>
                                                <th>Courier Date</th>
                                                <th>Timer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($phydocPending as $info)
                                                @if (Auth::user()->role == 'admin' ||
                                                        Auth::user()->role == 'coordinator' ||
                                                        Auth::user()->id == $info->team_member ||
                                                        (Auth::user()->role == 'team-leader' && Auth::user()->team == $info->users->team))
                                                    <tr>
                                                        <td>{{ $info->tender_no }}</td>
                                                        <td>{{ $info->tender_name }}</td>
                                                        <td>{{ $info->users->name }}</td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($info->due_date) }}</span>
                                                            {{ $info->due_date ? date('d-m-Y', strtotime($info->due_date)) : '' }}
                                                        </td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($info->dead_date) }}</span>
                                                            {{ $info->dead_date ? date('d-m-Y', strtotime($info->dead_date)) : '' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $timer = $info->getTimer('physical_docs');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $info->remainedTime('physical_docs');
                                                                }
                                                            @endphp
                                                            @if ($timer)
                                                                {{-- Sortable timer --}}
                                                                <span class="d-none">{{ $remaining }}</span>
                                                                <span class="timer" id="timer-{{ $info->id }}"
                                                                    data-remaining="{{ $remaining }}"></span>
                                                            @else
                                                                <span class="d-none">0</span>
                                                                {!! $remained !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($info->phydocs)
                                                                <a href="{{ route('phydocs.show', $info->phydocs->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    View
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('phydocs.edit', $info->id) }}"
                                                                class="btn btn-info btn-xs">
                                                                Submit Docs
                                                            </a>
                                                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator' || Auth::user()->role == 'account')
                                                                <form action="{{ route('phydocs.destroy', $info->id) }}"
                                                                    method="POST" style="display: inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <table class="table" id="allUsers">
                                        <thead class="">
                                            <tr>
                                                <th>Tender No</th>
                                                <th>Tender Name</th>
                                                <th>Team Member</th>
                                                <th>Due Date/Time</th>
                                                <th>Courier Date</th>
                                                <th>Timer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($phydocSent as $info)
                                                @if (Auth::user()->role == 'admin' ||
                                                        Auth::user()->role == 'coordinator' ||
                                                        Auth::user()->id == $info->team_member ||
                                                        (Auth::user()->role == 'team-leader' && Auth::user()->team == $info->users->team))
                                                    <tr>
                                                        <td>{{ $info->tender_no }}</td>
                                                        <td>{{ $info->tender_name }}</td>
                                                        <td>{{ $info->users->name }}</td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($info->due_date) }}</span>
                                                            {{ $info->due_date ? date('d-m-Y', strtotime($info->due_date)) : '' }}
                                                        </td>
                                                        <td>
                                                            <span class="d-none">{{ strtotime($info->dead_date) }}</span>
                                                            {{ $info->dead_date ? date('d-m-Y', strtotime($info->dead_date)) : '' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $timer = $info->getTimer('physical_docs');
                                                                if ($timer) {
                                                                    $start = $timer->start_time;
                                                                    $hrs = $timer->duration_hours;
                                                                    $end = strtotime($start) + $hrs * 60 * 60;
                                                                    $remaining = $end - time();
                                                                } else {
                                                                    $remained = $info->remainedTime('physical_docs');
                                                                }
                                                            @endphp
                                                            @if ($timer)
                                                                {{-- Sortable timer --}}
                                                                <span class="d-none">{{ $remaining }}</span>
                                                                <span class="timer" id="timer-{{ $info->id }}"
                                                                    data-remaining="{{ $remaining }}"></span>
                                                            @else
                                                                <span class="d-none">0</span>
                                                                {!! $remained !!}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($info->phydocs)
                                                                <a href="{{ route('phydocs.show', $info->phydocs->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    View
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('phydocs.edit', $info->id) }}"
                                                                class="btn btn-info btn-xs">
                                                                Submit Docs
                                                            </a>
                                                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator' || Auth::user()->role == 'account')
                                                                <form action="{{ route('phydocs.destroy', $info->id) }}"
                                                                    method="POST" style="display: inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs"
                                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
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
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
