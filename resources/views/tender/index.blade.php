@extends('layouts.app')
@section('page-title', 'All Tenders Info')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                @if (in_array('tender-create', $permissions))
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('tender.create') }}" class="btn btn-primary">Create New Tender</a>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <ul class="nav nav-pills justify-content-center" data-toggle="slider-tab" id="myTab"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="current-tab" data-bs-toggle="tab"
                                        data-bs-target="#pills-current" type="button" role="tab"
                                        aria-controls="current" aria-selected="true">Under Preparation</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="missed-tab" data-bs-toggle="tab"
                                        data-bs-target="#pills-missed" type="button" role="tab" aria-controls="missed"
                                        aria-selected="false">Did not Bid</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="bid-tab" data-bs-toggle="tab" data-bs-target="#pills-bid"
                                        type="button" role="tab" aria-controls="bid" aria-selected="false">Tender
                                        Bid</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="won-tab" data-bs-toggle="tab" data-bs-target="#pills-won"
                                        type="button" role="tab" aria-controls="won" aria-selected="false">Tender
                                        Won</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="lost-tab" data-bs-toggle="tab"
                                        data-bs-target="#pills-lost" type="button" role="tab" aria-controls="lost"
                                        aria-selected="false">Tender
                                        Lost</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-current" role="tabpanel"
                                    aria-labelledby="pills-current-tab1">
                                    <div class="table-responsive">
                                        <table class="table" id="allprep">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Organisation</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br> Items</th>
                                                    <th>Values <br> Incl. GST</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($prepTenders as $tender)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                        <tr>
                                                            <td>
                                                                {{ $tender->tender_no }}
                                                            </td>
                                                            <td>
                                                                {{ $tender->organizations ? $tender->organizations->name : '' }}
                                                            </td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td class="sorting_1">
                                                                <span style="display:none;">
                                                                    {{ strtotime($tender->due_date) }}
                                                                </span>
                                                                {{ $tender->due_date }} <br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td class="text-capitalize">
                                                                {{ $tender->statuses->name }}
                                                            </td>
                                                            <td>
                                                                @if ($tender->timer)
                                                                    @php
                                                                        $start = $tender->timer->start_time;
                                                                        $hrs = $tender->timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    @endphp
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    </span>
                                                                @else
                                                                    {!! $tender->remainedTime('tender_info_sheet') !!}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <a href="{{ route('tender.edit', $tender->id) }}"
                                                                        class="btn btn-info btn-xs">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('tender.show', $tender->id) }}"
                                                                        class="btn btn-primary btn-xs">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('tender.info.create', $tender->id) }}"
                                                                        class="btn btn-outline-info btn-xs">
                                                                        Fill
                                                                    </a>
                                                                    <a href="{{ route('extension.create', $tender->id) }}"
                                                                        class="btn btn-xs btn-secondary">
                                                                        Ext.Req.
                                                                    </a>
                                                                    <a href="{{ route('submit_query.create', $tender->id) }}"
                                                                        class="btn btn-xs btn-primary">
                                                                        Queries
                                                                    </a>
                                                                    <a href="#"
                                                                        onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();"
                                                                        class="btn btn-danger btn-xs">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('tender.destroy', $tender->id) }}"
                                                                        method="POST" id="deleteForm{{ $tender->id }}"
                                                                        style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $tender->id }}">
                                                                    </form>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-xs"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#exampleModal"
                                                                        data-id="{{ $tender->id }}"
                                                                        data-name="{{ $tender->status }}">
                                                                        Status <i class="fa-solid fa-rotate"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-missed" role="tabpanel"
                                    aria-labelledby="pills-missed-tab1">
                                    <div class="table-responsive">
                                        <table class="table" id="allMissed">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Organisation</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br> Items</th>
                                                    <th>Values <br> Incl. GST</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dnbTenders as $tender)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->organizations ? $tender->organizations->name : '' }}
                                                            </td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td class="sorting_1">
                                                                <span style="display:none;">
                                                                    {{ strtotime($tender->due_date) }}
                                                                </span>
                                                                {{ $tender->due_date }} <br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td class="text-capitalize">
                                                                {{ $tender->statuses->name }}
                                                            </td>
                                                            <td>
                                                                @if ($tender->timer)
                                                                    @php
                                                                        $start = $tender->timer->start_time;
                                                                        $hrs = $tender->timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    @endphp
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    </span>
                                                                @else
                                                                    {!! $tender->remainedTime('tender_info_sheet') !!}
                                                                @endif
                                                            </td>
                                                            <td class="d-flex flex-wrap gap-2">
                                                                <a href="{{ route('tender.edit', $tender->id) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('tender.show', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('tender.info.create', $tender->id) }}"
                                                                    class="btn btn-outline-info btn-xs">
                                                                    Fill
                                                                </a>
                                                                <a href="#"
                                                                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form action="{{ route('tender.destroy', $tender->id) }}"
                                                                    method="POST" id="deleteForm{{ $tender->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $tender->id }}">
                                                                </form>
                                                                <button type="button" class="btn btn-secondary btn-xs"
                                                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                                    data-id="{{ $tender->id }}"
                                                                    data-name="{{ $tender->status }}">
                                                                    Status <i class="fa-solid fa-rotate"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-bid" role="tabpanel"
                                    aria-labelledby="pills-bid-tab1">
                                    <div class="table-responsive">
                                        <table class="table" id="alltb">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Organisation</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br> Items</th>
                                                    <th>Values <br> Incl. GST</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tbTenders as $tender)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->organizations ? $tender->organizations->name : '' }}
                                                            </td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td class="sorting_1">
                                                                <span style="display:none;">
                                                                    {{ strtotime($tender->due_date) }}
                                                                </span>
                                                                {{ $tender->due_date }} <br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td class="text-capitalize">
                                                                {{ $tender->statuses->name }}
                                                            </td>
                                                            <td>
                                                                @if ($tender->timer)
                                                                    @php
                                                                        $start = $tender->timer->start_time;
                                                                        $hrs = $tender->timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    @endphp
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    </span>
                                                                @else
                                                                    {!! $tender->remainedTime('tender_info_sheet') !!}
                                                                @endif
                                                            </td>
                                                            <td class="d-flex flex-wrap gap-2">
                                                                <a href="{{ route('tender.edit', $tender->id) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('tender.show', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('tender.info.create', $tender->id) }}"
                                                                    class="btn btn-outline-info btn-xs">
                                                                    Fill
                                                                </a>
                                                                <a href="#"
                                                                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form action="{{ route('tender.destroy', $tender->id) }}"
                                                                    method="POST" id="deleteForm{{ $tender->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $tender->id }}">
                                                                </form>
                                                                <button type="button" class="btn btn-secondary btn-xs"
                                                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                                    data-id="{{ $tender->id }}"
                                                                    data-name="{{ $tender->status }}">
                                                                    Status <i class="fa-solid fa-rotate"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-won" role="tabpanel"
                                    aria-labelledby="pills-won-tab1">
                                    <div class="table-responsive">
                                        <table class="table" id="alltw">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Organisation</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br> Items</th>
                                                    <th>Values <br> Incl. GST</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($twTenders as $tender)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->organizations ? $tender->organizations->name : '' }}
                                                            </td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td class="sorting_1">
                                                                <span style="display:none;">
                                                                    {{ strtotime($tender->due_date) }}
                                                                </span>
                                                                {{ $tender->due_date }} <br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td class="text-capitalize">
                                                                {{ $tender->statuses->name }}
                                                            </td>
                                                            <td>
                                                                @if ($tender->timer)
                                                                    @php
                                                                        $start = $tender->timer->start_time;
                                                                        $hrs = $tender->timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    @endphp
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    </span>
                                                                @else
                                                                    {!! $tender->remainedTime('tender_info_sheet') !!}
                                                                @endif
                                                            </td>
                                                            <td class="d-flex flex-wrap gap-2">
                                                                <a href="{{ route('tender.edit', $tender->id) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('tender.show', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('tender.info.create', $tender->id) }}"
                                                                    class="btn btn-outline-info btn-xs">
                                                                    Fill
                                                                </a>
                                                                <a href="#"
                                                                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form action="{{ route('tender.destroy', $tender->id) }}"
                                                                    method="POST" id="deleteForm{{ $tender->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $tender->id }}">
                                                                </form>
                                                                <button type="button" class="btn btn-secondary btn-xs"
                                                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                                    data-id="{{ $tender->id }}"
                                                                    data-name="{{ $tender->status }}">
                                                                    Status <i class="fa-solid fa-rotate"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-lost" role="tabpanel"
                                    aria-labelledby="pills-lost-tab1">
                                    <div class="table-responsive">
                                        <table class="table" id="alltl">
                                            <thead class="">
                                                <tr>
                                                    <th>Tender No</th>
                                                    <th>Organisation</th>
                                                    <th>Tender Name</th>
                                                    <th>Tender <br> Items</th>
                                                    <th>Values <br> Incl. GST</th>
                                                    <th>Tender <br> Fees</th>
                                                    <th>EMD</th>
                                                    <th>Team <br> Member</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tlTenders as $tender)
                                                    @if (Auth::user()->role == 'admin' ||
                                                            Auth::user()->role == 'coordinator' ||
                                                            Auth::user()->id == $tender->team_member ||
                                                            (Auth::user()->role == 'team-leader' && Auth::user()->team == $tender->users->team))
                                                        <tr>
                                                            <td>{{ $tender->tender_no }}</td>
                                                            <td>{{ $tender->organizations ? $tender->organizations->name : '' }}
                                                            </td>
                                                            <td>{{ $tender->tender_name }}</td>
                                                            <td>{{ $tender->itemName ? $tender->itemName->name : '' }}</td>
                                                            <td>{{ format_inr($tender->gst_values) }}</td>
                                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                                            <td>{{ format_inr($tender->emd) }}</td>
                                                            <td>{{ $tender->users->name }}</td>
                                                            <td class="sorting_1">
                                                                <span style="display:none;">
                                                                    {{ strtotime($tender->due_date) }}
                                                                </span>
                                                                {{ $tender->due_date }} <br>
                                                                {{ date('h:i A', strtotime($tender->due_time)) }}
                                                            </td>
                                                            <td class="text-capitalize">
                                                                {{ $tender->statuses->name }}
                                                            </td>
                                                            <td>
                                                                @if ($tender->timer)
                                                                    @php
                                                                        $start = $tender->timer->start_time;
                                                                        $hrs = $tender->timer->duration_hours;
                                                                        $end = strtotime($start) + $hrs * 60 * 60;
                                                                        $remaining = $end - time();
                                                                    @endphp
                                                                    <span class="timer" id="timer-{{ $tender->id }}"
                                                                        data-remaining="{{ $remaining }}">
                                                                    </span>
                                                                @else
                                                                    {!! $tender->remainedTime('tender_info_sheet') !!}
                                                                @endif
                                                            </td>
                                                            <td class="d-flex flex-wrap gap-2">
                                                                <a href="{{ route('tender.edit', $tender->id) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="{{ route('tender.show', $tender->id) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('tender.info.create', $tender->id) }}"
                                                                    class="btn btn-outline-info btn-xs">
                                                                    Fill
                                                                </a>
                                                                <a href="#"
                                                                    onclick="event.preventDefault(); document.getElementById('deleteForm{{ $tender->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form action="{{ route('tender.destroy', $tender->id) }}"
                                                                    method="POST" id="deleteForm{{ $tender->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $tender->id }}">
                                                                </form>
                                                                <button type="button" class="btn btn-secondary btn-xs"
                                                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                                    data-id="{{ $tender->id }}"
                                                                    data-name="{{ $tender->status }}">
                                                                    Status <i class="fa-solid fa-rotate"></i>
                                                                </button>
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
        </div>
    </section>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Status</h5>
                </div>
                <form action="{{ route('tender.updateStatus') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" @class(['form-conttol'])>
                        <select name="status" id="status" @class(['form-control'])>
                            @php
                                $statuses = App\Models\Status::all();
                            @endphp
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#exampleModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var name = button.data('name');
                var modal = $(this);
                modal.find('.modal-body #id').val(id);
                // select the option with a matching value
                modal.find('.modal-body #status option[value="' + name + '"]').prop('selected', true);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });

        $(document).ready(function() {
            // destroy datatable if exists on .table
            if ($.fn.dataTable.isDataTable('.table')) {
                // $('.table').DataTable().destroy();
            }
            // allprep, allMissed, alltb, alltw, alltl
        })
    </script>
@endpush
