@extends('layouts.app')

@section('page-title', 'Pricing Sheet')
@php
    $sheetTypes = [
        '1' => 'Battery',
        '2' => 'Charger',
        '3' => 'Others (new sheet)',
    ];
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('pricingsheets.create') }}" class="btn btn-sm btn-primary">Create Pricing Sheet</a>
                </div>
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
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tender Id</th>
                                        <th>Sheet Type</th>
                                        <th>Step 2</th>
                                        <th>Step 3</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sheets as $sheet)
                                        <tr>
                                            <td>{{ $sheet->tenderInfo->tender_no }}</td>
                                            <td>{{ $sheetTypes[$sheet->sheet_type] }}</td>
                                            <td>
                                                <a href="{{ route('pricingsheets.get.step2', $sheet->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    @php
                                                        $label = match ($sheet->sheet_type) {
                                                            1 => 'Battery Accessories',
                                                            2 => 'Charger Accessories',
                                                            3 => 'Others',
                                                        };
                                                    @endphp
                                                    {{ $label }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('pricingsheets.get.step3', $sheet->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    @php
                                                        $label = match ($sheet->sheet_type) {
                                                            1 => 'Battery I&C',
                                                            2 => 'Charger I&C',
                                                            3 => 'Others',
                                                        };
                                                    @endphp
                                                    {{ $label }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('pricingsheets.show', $sheet->id) }}"
                                                    class="btn btn-sm btn-primary">View</a>
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
            },
            pageLength: 50,
        });
    </script>
@endpush
