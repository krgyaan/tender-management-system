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
                                        @if ($sheet_type == '1')
                                            <div id="battery_form" class="row">
                                                @csrf
                                                <div class="col-md-3 form-group">
                                                    <label for="bg">BG</label>
                                                    <input type="text" name="bg" id="bg" class="form-control"
                                                        value="{{ $bg }}" readonly>
                                                </div>
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
                                                <div class="col-md-12">
                                                    <div class="table-responsive mb-3" id="battery1">
                                                        <table class="table-bordered table-striped table-hover" style="width: 100%;">
                                                            <div class="text-end">
                                                                <button type="button" class="btn btn-sm btn-secondary"
                                                                    id="add_battery">
                                                                    Add Item
                                                                </button>
                                                            </div>
                                                            <thead>
                                                                <tr>
                                                                    <th>SN</th>
                                                                    <th>Item Name</th>
                                                                    <th>Model</th>
                                                                    <th>AH</th>
                                                                    <th>Cells/Bank</th>
                                                                    <th>Spare Cell</th>
                                                                    <th>Price/AH</th>
                                                                    <th>No. of Banks</th>
                                                                    <th><i class="fa fa-list"></i></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="battery_step1">
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td>
                                                                        <input type="text" name="items[0][item_name]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <select name="items[0][model]" class="form-control">
                                                                            <option value="">--</option>
                                                                            @foreach ($models as $model => $value)
                                                                                <option value="{{ $model }}">
                                                                                    {{ $value }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="items[0][ah]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="items[0][cells]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="items[0][spare_cell]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="items[0][price]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="items[0][banks]"
                                                                            class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                            id="remove_battery">
                                                                            <i class="fa fa-minus"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ URL::previous() }}" class="btn btn-outline-light">
                                        Back
                                    </a>
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
        $(document).ready(function() {
            $('#add_battery').click(function() {
                let options = `
                <option value="">--</option>
                @foreach ($models as $model => $value)
                    <option value="{{ $model }}">
                        {{ $value }}
                    </option>
                @endforeach
                `;
                let len = $('#battery_step1 tr').length;
                $('#battery_step1').append(
                    '<tr>' +
                    '<td>' + (len + 1) + '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][item_name]" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<select name="items[' + len + '][model]" class="form-control">' + options +
                    '</select>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][ah]" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][cells]" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][spare_cell]" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][price]" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="items[' + len + '][banks]" class="form-control">' +
                    '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm" id="remove_battery"><i class="fa fa-minus"></i></button></td>' +
                    '</tr>'
                );
            });

            $('#battery_step1').on('click', '#remove_battery', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endpush

@push('styles')
    <style>
        th,
        td {
            padding: 8px;
            font-size: 14px;
        }
    </style>
@endpush