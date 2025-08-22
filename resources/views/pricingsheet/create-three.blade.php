@extends('layouts.app')
@section('page-title', 'Pricing Sheet (Step 2)')

@php
    $models = [
        'kph' => 'KPH',
        'kpm' => 'KPM',
        'kpl' => 'KPL',
        'rgsl' => 'RGSL',
        'vrrm' => 'VRRM',
    ];
    $accessories = [
        1 => 'DC Cable',
        2 => 'AC Cable',
        3 => 'Lugs',
        4 => 'Glands',
        5 => 'Earthing Strip',
        6 => 'Cable tray',
        7 => 'Earthing Rod + Chamber',
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
                            <form method="POST" action="{{ route('pricingsheets.post.step2') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $sheet->id }}">
                                <div class="row" id="conditional_form">
                                    @if ($sheet->sheet_type == '1')
                                        <div id="battery_accessories_form" class="row">
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
                                                        <div class="table-responsive mb-3" id="battery_{{ $loop->index }}">
                                                            <table class="table-bordered table-striped table-hover" style="width: 100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Accessories</th>
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
                                                                <tbody id="accessories_{{ $loop->index }}">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="hidden"
                                                                                name="items[{{ $itemIndex }}][item_id]"
                                                                                value="{{ $item->id }}">
                                                                            <select
                                                                                name="items[{{ $itemIndex }}][accessories][0][item]"
                                                                                class="form-control">
                                                                                <option value="">-- Select --</option>
                                                                                @foreach ($accessories as $key => $value)
                                                                                    <option value="{{ $key }}">
                                                                                        {{ $value }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number"
                                                                                name="items[{{ $itemIndex }}][accessories][0][price]"
                                                                                min="0" step="0.01"
                                                                                id="price_{{ $loop->index }}"
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
                const len = $(`#accessories_${itemId} tr`).length;
                const options = `
                <option value="">-- Select --</option>
                @foreach ($accessories as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
                `;
                $(`#accessories_${itemId}`).append(`
                <tr>
                    <td>
                        <select name="items[${itemId}][accessories][${len}][item]" class="form-control">${options}</select>
                    </td>
                    <td>
                        <input type="number" name="items[${itemId}][accessories][${len}][price]" class="form-control" min="0" step="0.01" id="price_${itemId}">
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