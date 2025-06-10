@extends('layouts.app')
@section('page-title', 'Physical Docs Details')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center pb-2">Physical Docs Details</h4>
                </div>
                {{ $phyDocs }}
                <div class="card-body m-auto">
                    <div class="table-responsive">
                        @if ($phyDocs)
                            @foreach ($phyDocs as $docs)
                                <table class="table-bordered border w-75 mb-5">
                                    <tbody>
                                        @if ($docs->persons)
                                            @foreach ($docs->persons as $person)
                                                <tr>
                                                    <th>Person Name</th>
                                                    <td>{{ $person->name }}</td>
                                                    <th>Person Mobile</th>
                                                    <td>{{ $person->phone }}</td>
                                                    <th>Person Email</th>
                                                    <td>{{ $person->email }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if ($docs->courier)
                                            <tr>
                                                <th>Courier Provider</th>
                                                <td>{{ $docs->courier->courier_provider }}</td>
                                                <th>Docket No.</th>
                                                <td>{{ $docs->courier->docket_no }}</td>
                                                <th>Docket Slip</th>
                                                <td>{{ $docs->courier->docket_slip }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        th,
        td {
            padding: 8px;
            font-size: 14px;
        }
    </style>
@endpush
