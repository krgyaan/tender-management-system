@extends('layouts.app')
@section('page-title', 'Pricing Sheet Create')
@php
    $models = [
        'kph' => 'KPH',
        'kpm' => 'KPM',
        'kpl' => 'KPL',
        'rgsl' => 'RGSL',
        'vrrm' => 'VRRM',
    ];
@endphp
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="new-sheet-info">
                            <form method="POST" action="{{ route('pricingsheets.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12" id="conditional_form">
                                        @if ($sheet->sheet_type == '1')
                                            <div id="battery_form" class="row">
                                                @csrf
                                                <div class="col-md-3 form-group">
                                                    <label for="freight">Freight %</label>
                                                    <input type="number" name="freight_per" id="freight_per" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="cash_margin">Cash Margin</label>
                                                    <input type="number" name="cash_margin" id="cash_margin" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="gst_battery">GST on Battery (%)</label>
                                                    <input type="number" name="gst_battery" id="gst_battery" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="gst_ic">GST on I&C (%)</label>
                                                    <input type="number" name="gst_ic" id="gst_ic" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="gst_buyback">GST on Buyback (%)</label>
                                                    <input type="number" name="gst_buyback" id="gst_buyback" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {});
    </script>
@endpush
