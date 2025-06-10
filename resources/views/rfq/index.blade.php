@extends('layouts.app')
@section('page-title', 'All RFQs')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('rfq.create') }}" class="btn btn-primary btn-sm">Raise RFQ</a>
                    <a href="{{ route('rfq.receipt') }}" class="btn btn-primary btn-sm">Receipt Dashboard</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">RFQ</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">RFQ Sumnitted</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead>
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Item Name</th>
                                                    <th>RFQ to</th>
                                                    <th>Due Date <br> and Time</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($pendingRfqs as $tender)
                                                    @php
                                                        $canView =
                                                            Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' &&
                                                                Auth::user()->team == optional($tender->users)->team);
                                                    @endphp

                                                    @if ($canView)
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ optional($tender->itemName)->name }}</td>

                                                            <td>
                                                                @foreach (explode(',', $tender->rfq_to ?? '') as $vendorId)
                                                                    @php
                                                                        $vendor = \App\Models\VendorOrg::find(
                                                                            $vendorId,
                                                                        );
                                                                    @endphp
                                                                    @if ($vendor)
                                                                        {{ $vendor->name }}<br>
                                                                    @endif
                                                                @endforeach
                                                            </td>

                                                            <td>
                                                                <span
                                                                    class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                                {{ $tender->due_date ? date('d-m-Y', strtotime($tender->due_date)) : '' }}
                                                            </td>

                                                            <td>
                                                                @php
                                                                    $timer = $tender->getTimer('rfq');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 3600;
                                                                        $remaining = $end - time(); // in seconds
                                                                    } else {
                                                                        $remained = $tender->remainedTime('rfq');
                                                                    }
                                                                @endphp

                                                                @if ($timer)
                                                                    {{-- Sortable timer --}}
                                                                    <span class="d-none">{{ $remaining }}</span>
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    <span class="d-none">0</span>
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <a href="{{ route('rfq.create', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    Send RFQ
                                                                </a>

                                                                @if ($tender->rfqs)
                                                                    <a href="{{ route('rfq.recipient', $tender->rfqs) }}"
                                                                        class="btn btn-success btn-xs">
                                                                        Receipt
                                                                    </a>
                                                                    <a href="{{ route('rfq.show', $tender->rfqs) }}"
                                                                        class="btn btn-primary btn-xs">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @endif

                                                                @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account']))
                                                                    <form action="{{ route('rfq.destroy', $tender->id) }}"
                                                                        method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-xs"
                                                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No tenders found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table" id="allUsers">
                                            <thead>
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Tender Name</th>
                                                    <th>Item Name</th>
                                                    <th>RFQ to</th>
                                                    <th>Due Date <br> and Time</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($sentRfqs as $tender)
                                                    @php
                                                        $canView =
                                                            Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' &&
                                                                Auth::user()->team == optional($tender->users)->team);
                                                    @endphp

                                                    @if ($canView)
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ optional($tender->itemName)->name }}</td>

                                                            <td>
                                                                @foreach (explode(',', $tender->rfq_to ?? '') as $vendorId)
                                                                    @php
                                                                        $vendor = \App\Models\VendorOrg::find(
                                                                            $vendorId,
                                                                        );
                                                                    @endphp
                                                                    @if ($vendor)
                                                                        {{ $vendor->name }}<br>
                                                                    @endif
                                                                @endforeach
                                                            </td>

                                                            <td>
                                                                <span
                                                                    class="d-none">{{ strtotime($tender->due_date) }}</span>
                                                                {{ $tender->due_date ? date('d-m-Y', strtotime($tender->due_date)) : '' }}
                                                            </td>

                                                            <td>
                                                                @php
                                                                    $timer = $tender->getTimer('rfq');
                                                                    if ($timer) {
                                                                        $start = $timer->start_time;
                                                                        $hrs = $timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 3600;
                                                                        $remaining = $end - time(); // in seconds
                                                                    } else {
                                                                        $remained = $tender->remainedTime('rfq');
                                                                    }
                                                                @endphp

                                                                @if ($timer)
                                                                    {{-- Sortable timer --}}
                                                                    <span class="d-none">{{ $remaining }}</span>
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}"></span>
                                                                @else
                                                                    <span class="d-none">0</span>
                                                                    {!! $remained !!}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <a href="{{ route('rfq.create', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    Send RFQ
                                                                </a>

                                                                @if ($tender->rfqs)
                                                                    <a href="{{ route('rfq.recipient', $tender->rfqs) }}"
                                                                        class="btn btn-success btn-xs">
                                                                        Receipt
                                                                    </a>
                                                                    <a href="{{ route('rfq.show', $tender->rfqs) }}"
                                                                        class="btn btn-primary btn-xs">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @endif

                                                                @if (in_array(Auth::user()->role, ['admin', 'coordinator', 'account']))
                                                                    <form action="{{ route('rfq.destroy', $tender->id) }}"
                                                                        method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-xs"
                                                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No tenders found.</td>
                                                    </tr>
                                                @endforelse
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
