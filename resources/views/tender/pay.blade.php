@extends('layouts.app')
@section('page-title', 'All Tenders Info')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">

                        @if (Session::has('success'))
                            <div class="alert alert-success" role="alert">
                                {{ Session::get('success') }}
                            </div>
                        @endif

                        @if (Session::has('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ Session::get('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table" id="allUsers">
                                <thead class="">
                                    <tr>
                                        <th>Tender No</th>
                                        <th>Organisation</th>
                                        <th>Tender <br> Name</th>
                                        <th>Team <br> Member</th>
                                        <th>Tender <br> Items</th>
                                        <th>Due <br> DateTime</th>
                                        <th>EMD</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenderInfo as $tender)
                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'coordinator' || Auth::user()->id == $tender->team_member)
                                            <tr>
                                                <td>{{ $tender->tender_no }}</td>
                                                <td>{{ $tender->organizations ? $tender->organizations->name : '' }}</td>
                                                <td>{{ $tender->tender_name }}</td>
                                                <td>{{ $tender->users->name }}</td>
                                                <td>
                                                    @foreach ($tender->items as $i)
                                                        {{ $i->itemName ? $i->itemName->name : '' }}<br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    {{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                                    {{ date('h:i A', strtotime($tender->due_time)) }}
                                                </td>
                                                <td>{{ $tender->emd }}</td>
                                                <td>
                                                    <a href="{{ route('pay.edit', $tender->id) }}"
                                                        class="btn btn-info btn-xs">
                                                        Fill Next
                                                    </a>
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
    </section>
@endsection

@push('scripts')
    <script>
        new DataTable('#allUsers', {
            info: false,
            ordering: true,
            paging: true,
            layout: {
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Pagination CSS */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25em 0.5em !important;
            border-radius: 0.25em !important;
        }

        .dt-paging-button.current {
            background-color: #fff !important;
            color: #000 !important;
        }

        .dt-paging-button {
            border: 1px solid #fff !important;
            background-color: #aaa !important;
        }

        th,
        td {
            font-size: 12px;
        }
    </style>
@endpush
