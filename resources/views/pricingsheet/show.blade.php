@extends('layouts.app')

@section('page-title', 'Pricing Sheet Show')

@php
    $sheetTypes = [
        '1' => 'Battery',
        '2' => 'Charger',
        '3' => 'Others (new sheet)',
    ];
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
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Tender Info</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tender No</th>
                                                <th>Tender Name</th>
                                                <th>Assigned To</th>
                                                <th>Due DateTime</th>
                                                <th>Tender Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $sheet->tenderInfo->tender_no }}</td>
                                                <td>{{ $sheet->tenderInfo->tender_name }}</td>
                                                <td>{{ $sheet->tenderInfo->users->name }}</td>
                                                <td>{{ $sheet->tenderInfo->due_date }}<br>{{ $sheet->tenderInfo->due_time }}
                                                </td>
                                                <td>{{ $sheet->tenderInfo->statuses->name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Comperative Sheet Info</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Freight %</th>
                                                <th>Cash Margin</th>
                                                <th>GST on Battery</th>
                                                <th>GST on I&C</th>
                                                <th>GST on Buyback</th>
                                                <th>BG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $sheet->freight_per }}</td>
                                                <td>{{ $sheet->cash_margin }}</td>
                                                <td>{{ $sheet->gst_battery }}</td>
                                                <td>{{ $sheet->gst_ic }}</td>
                                                <td>{{ $sheet->gst_buyback }}</td>
                                                <td>{{ $sheet->bg }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h5>Sheet Items Details</h5>
                                <div class="table-responsive">
                                    @foreach ($sheet->items as $item)
                                        <table class="table table-bordered table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Item <br>Name</th>
                                                    <th>Model</th>
                                                    <th>AH</th>
                                                    <th>Cells<br> per Bank</th>
                                                    <th>Spare <br>Cell</th>
                                                    <th>Total <br>Cells</th>
                                                    <th>Total <br>AH</th>
                                                    <th>Price<br> per AH</th>
                                                    <th>Amount</th>
                                                    <th>No. of <br>Banks</th>
                                                    <th>Freight</th>
                                                    <th>Total <br>(Incl. Freight)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $item->id }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $models[$item->model] }}</td>
                                                    <td>{{ $item->ah }}</td>
                                                    <td>{{ $item->cells }}</td>
                                                    <td>{{ $item->spare_cell }}</td>
                                                    @php
                                                        $totalCells = $item->cells + $item->spare_cell;
                                                        $totalAh = $totalCells * $item->ah;
                                                        $totalPrice = $totalAh * $item->price;
                                                        $totalFreight = $totalPrice * $sheet->freight_per;
                                                        $priceFreight = $totalPrice + $totalFreight;
                                                    @endphp
                                                    <td>{{ $totalCells }}</td>
                                                    <td>{{ $totalAh }}</td>
                                                    <td>{{ $item->price }}</td>
                                                    <td>{{ $totalPrice }}</td>
                                                    <td>{{ $item->banks }}</td>
                                                    <td>{{ $totalFreight }}</td>
                                                    <td>{{ $priceFreight }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7">
                                                        <table class="table table-sm table-bordered w-100">
                                                            <h6>Accessories</h6>
                                                            <tbody>
                                                                @foreach ($item->accessories as $acc)
                                                                    <tr>
                                                                        <th style="width: 250px; !important;">
                                                                            {{ $accessories[$acc->acc_name] }}</th>
                                                                        <td>{{ $acc->price }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="fw-bold">
                                                                    <th class="text-end">Total</th>
                                                                    <td>{{ $item->acc_total }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td colspan="7">
                                                        <table class="table table-sm table-bordered w-100">
                                                            <h6>ICs</h6>
                                                            <tbody>
                                                                @foreach ($item->ics as $ic)
                                                                    <tr>
                                                                        <th style="width: 250px; !important;">
                                                                            {{ $ics[$ic->ic_name] }}</th>
                                                                        <td>{{ $ic->price }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="fw-bold">
                                                                    <th class="text-end">Total</th>
                                                                    <td>{{ $item->ic_cost }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <table class="table table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Bidding Installation Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Bidding Battery Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total Amount Battery
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total I&C
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total Battery Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total I&C Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        GST on Battery
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        GST on I&C
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        GST on Buyback
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td colspan="7">
                                                        <table class="table table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Dry Weight of Old Battery bank (Kg)
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total Weight
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Buyback Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total Buy Back
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Buyback Cost
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Total Buyback
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        UNIT Basic Price after deducting buyback
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        UNIT Tax Inclusive Price after deducting Buyback
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                                <tr>
                                                                    <th style="width: 250px !important;">
                                                                        Tax Inclusive Price after deducting Buyback
                                                                    </th>
                                                                    <td>0</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @php
                            echo '<pre>' . print_r($sheet->toArray(), true) . '</pre>';
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('styles')
    <style>
        th,
        td {
            font-size: 12px;
        }
    </style>
@endpush
