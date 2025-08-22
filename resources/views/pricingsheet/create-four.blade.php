@extends('layouts.app')
@section('page-title', 'Pricing Sheet (Step 3)')

@php
    $models = [
        'kph' => 'KPH',
        'kpm' => 'KPM',
        'kpl' => 'KPL',
        'rgsl' => 'RGSL',
        'vrrm' => 'VRRM',
    ];
    $ics = [
        1 => 'Dismantling',
        2 => 'Instt & Comm.',
        3 => 'Unloading',
        4 => 'Shifting of Old Bank',
        5 => 'Electrolyte Discharge',
        6 => 'Loading of Old Battery Bank',
        7 => 'Accessories',
        8 => 'TPI',
        9 => 'OEM Service Engineer',
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

                        <div class="new-sheet-info">
                            <form method="POST" action="{{ route('pricingsheets.post.step3') }}">
                                @csrf
                                <div class="row" id="conditional_form">
                                    @if ($sheet->sheet_type == '1')
                                        <input type="hidden" name="id" value="{{ $sheet->id }}">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="model">Bidding Installtion Cost</label>
                                                    <input type="number" name="bic" id="bic" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="buyback">Buyback (Y/N)</label>
                                                    <select name="buyback" id="buyback" class="form-control">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4" id="field1">
                                                    <label for="dry_weight">Dry Weight of Old Battery bank (Kg)</label>
                                                    <input type="number" name="dry_weight" id="dry_weight" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4" id="field2">
                                                    <label for="buyback_rs">Buyback Rs./Kg</label>
                                                    <input type="number" name="buyback_rs" id="buyback_rs" min="0"
                                                        step="0.01" class="form-control">
                                                </div>
                                                <div class="form-group col-md-4" id="field3">
                                                    <label for="act_buyback_rs">Actual Buyback Rs./Kg</label>
                                                    <input type="number" name="act_buyback_rs" id="act_buyback_rs"
                                                        min="0" step="0.01" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="battery_ic_form" class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    @foreach ($sheet->items as $itemIndex => $item)
                                                        <div class="d-flex align-items-center justify-content-between py-2">
                                                            <div>Item Name: <span
                                                                    class="text-white ps-4">{{ $item->item_name }}</span>
                                                            </div>
                                                            <div>Model: <span
                                                                    class="text-white ps-4">{{ $item->model }}</span></div>
                                                            <div>AH: <span
                                                                    class="text-white ps-4">{{ $item->ah }}</span></div>
                                                            <div>Cells/Bank: <span
                                                                    class="text-white ps-4">{{ $item->cells }}</span></div>
                                                        </div>
                                                        <div class="table-responsive" id="battery_{{ $loop->index }}">
                                                            <table class="table-bordered table-striped table-hover mb-3" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Price</th>
                                                                        <th>
                                                                            <button type="button"
                                                                                class="btn btn-primary btn-sm add-accessory"
                                                                                data-item-id="{{ $loop->index }}">
                                                                                <i class="fa fa-plus"></i>
                                                                            </button>
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="ic_{{ $loop->index }}">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="items[{{ $itemIndex }}][item_id]"
                                                                                value="{{ $item->id }}">
                                                                            <select
                                                                                name="items[{{ $itemIndex }}][ic][0][item]"
                                                                                class="form-control">
                                                                                <option value="">-- Select --
                                                                                </option>
                                                                                @foreach ($ics as $key => $value)
                                                                                    <option value="{{ $key }}">
                                                                                        {{ $value }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                name="items[{{ $itemIndex }}][ic][0][price]"
                                                                                class="form-control">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm remove-accessory">
                                                                                <i class="fa fa-minus"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
            // Add accessory
            $('.add-accessory').click(function() {
                const itemId = $(this).data('item-id');
                const len = $(`#ic_${itemId} tr`).length;
                const options = `
                <option value="">-- Select --</option>
                @foreach ($ics as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
                `;
                $(`#ic_${itemId}`).append(`
                <tr>
                    <td>
                        <select name="items[${itemId}][ic][${len}][item]" class="form-control">${options}</select>
                    </td>
                    <td>
                        <input type="number" name="items[${itemId}][ic][${len}][price]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-accessory">
                            <i class="fa fa-minus"></i>
                        </button>
                    </td>
                </tr>
                `);
            });

            // Remove accessory
            $(document).on('click', '.remove-accessory', function() {
                $(this).closest('tr').remove();
            });

            // selectd #buyback == 1 then
            $('#field1').hide();
            $('#field2').hide();
            $('#field3').hide();
            $('#buyback').on('change', function() {
                if ($(this).val() == 1) {
                    $('#field1').show();
                    $('#field2').show();
                    $('#field3').show();
                } else {
                    $('#field1').hide();
                    $('#field2').hide();
                    $('#field3').hide();
                }
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