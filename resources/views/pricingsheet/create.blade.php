@extends('layouts.app')

@section('page-title', 'Pricing Sheet Create')

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
                    <a href="{{ route('pricingsheets.index') }}" class="btn btn-sm btn-primary">View All Pricing Sheets</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="{{ route('pricingsheets.post.step1') }}" method="POST" class="row align-items-end">
                            @csrf
                            @php
                                $step1 = Session::get('step1');
                            @endphp
                            <div class="form-group col-md-4">
                                <label for="tender_no">Tender No</label>
                                <select class="form-control" name="tender_no" id="tender_no">
                                    <option value="">-- Select --</option>
                                    @foreach ($tenderInfo as $tender)
                                        <option
                                            {{ isset($step1['tender_no']) && $step1['tender_no'] == $tender->id ? 'selected' : '' }}
                                            value="{{ $tender->id }}">{{ $tender->tender_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sheet_type">Comparative Sheet Type</label>
                                <select class="form-control" name="sheet_type" id="sheet_type">
                                    <option value="">-- Select --</option>
                                    @foreach ($sheetTypes as $key => $value)
                                        <option
                                            {{ isset($step1['sheet_type']) && $step1['sheet_type'] == $key ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <button type="submit" name="submit" class="btn btn-primary">Next</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
