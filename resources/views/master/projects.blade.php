@extends('layouts.app')
@section('page-title', 'TQ Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">TQ Pending</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">TQ Submitted</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tqPending as $key => $row)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $row->tender_no }}</td>
                                                        <td>{{ $row->tender_name }}</td>
                                                        <td>
                                                            {{ $row->bs ? date('d-m-Y H:i A', strtotime($row->bs->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td>{{ $row->statuses->name }}</td>
                                                        <td></td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <a href="{{ asset('admin/view_butten/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    View
                                                                </a>
                                                                <a href="{{ asset('admin/tq_received_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    TQ Received
                                                                </a>
                                                                <a href="{{ asset('admin/tq_replied_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-secondary btn-xs">
                                                                    TQ Replied
                                                                </a>
                                                                <br>
                                                                <a href="{{ asset('admin/tq_missed_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class=" mt-1 btn btn-danger btn-xs">
                                                                    TQ Missed
                                                                </a>
                                                                <a href=""
                                                                    class=" mt-1 btn btn-warning btn-xs">Qualified</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Tender No.</th>
                                                    <th>Tender Name</th>
                                                    <th>Bid Submission Date</th>
                                                    <th>Status</th>
                                                    <th>Timer</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tqSubmitted as $key => $row)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $row->tender_no }}</td>
                                                        <td>{{ $row->tender_name }}</td>
                                                        <td>
                                                            {{ $row->bs ? date('d-m-Y H:i A', strtotime($row->bs->bid_submissions_date)) : '' }}
                                                        </td>
                                                        <td>{{ $row->statuses->name }}</td>
                                                        <td></td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <a href="{{ asset('admin/view_butten/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-info btn-xs">
                                                                    View
                                                                </a>
                                                                <a href="{{ asset('admin/tq_received_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-primary btn-xs">
                                                                    TQ Received
                                                                </a>
                                                                <a href="{{ asset('admin/tq_replied_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class="btn btn-secondary btn-xs">
                                                                    TQ Replied
                                                                </a>
                                                                <br>
                                                                <a href="{{ asset('admin/tq_missed_form/' . Crypt::encrypt($row->id)) }}"
                                                                    class=" mt-1 btn btn-danger btn-xs">
                                                                    TQ Missed
                                                                </a>
                                                                <a href=""
                                                                    class=" mt-1 btn btn-warning btn-xs">Qualified</a>
                                                            </div>
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
    </section>
@endsection
