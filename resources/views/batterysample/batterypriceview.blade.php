@extends('layouts.app')
@section('page-title', 'Battery Price Sheet View')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <button class="btn btn-primary btn-xs" onclick="exportTableToExcel('basic21', 'Battery_Price')">
                                Download Excel
                            </button>
                            <div class="table-responsive">
                                <table id="basic21" class="table-striped dataTable border table-bordered"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.no.</th>
                                            <th>Item Name</th>
                                            <th>Model</th>
                                            <th>AH</th>
                                            <th>Cells per Bank</th>
                                            <th>Spare Cells</th>
                                            <th>Total Cells</th>
                                            <th>Total AH</th>
                                            <th>Price/AH</th>
                                            <th>Amount</th>
                                            <th>Freight</th>
                                            <th>Total <br>(Incl. Freight)</th>
                                            <th>Installation</th>
                                            <th>Cost per Bank</th>
                                            <th>Selling Price</th>
                                            <th>Bidding Installtion Cost</th>
                                            <th>Bidding Battery Cost</th>
                                            <th>No. of Banks</th>
                                            <th>Total Amount Battery</th>
                                            <th>Total I&C </th>
                                            <th>Total Battery Cost</th>
                                            <th>Total I&C Cost</th>
                                            <th>Dry Weight of <br>Old Battery bank (Kg)</th>
                                            <th>Weight per bank</th>
                                            <th>Total Weight</th>
                                            <th>Buyback Cost</th>
                                            <th>Total Buy Back</th>
                                            <th>Buyback Cost</th>
                                            <th>Total Buyback</th>
                                            <th>GST on Battery</th>
                                            <th>GST on I&C</th>
                                            <th>GST on Buyback</th>
                                            <th>UNIT Basic Price <br>after deducting buyback</th>
                                            <th>UNIT Tax Inclusive <br>Price after deducting Buyback</th>
                                            <th>Basic Price after <br>deducting buyback</th>
                                            <th>Tax Inclusive Price <br>after deducting Buyback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($batteryprice as $key => $batterypriceData)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $batterypriceData->item_name }}</td>
                                                <td>
                                                    @if (isset($batterypriceData->itemModel))
                                                        {{ $batterypriceData->itemModel->model }}
                                                    @endif
                                                </td>
                                                <td>{{ $batterypriceData->ah }}</td>
                                                <td>{{ $batterypriceData->cells_per_bank }}</td>
                                                <td>{{ $batterypriceData->spare_cells }}</td>
                                                @php
                                                    $totalCells = 0;
                                                    $totalCells =
                                                        $batterypriceData->spare_cells +
                                                        $batterypriceData->cells_per_bank;
                                                @endphp
                                                <td>{{ $totalCells }}</td>
                                                @php
                                                    $TotalAH = 0;
                                                    $TotalAH = $totalCells * $batterypriceData->ah;
                                                @endphp
                                                <td>{{ $TotalAH }}</td>
                                                <td>{{ $batterypriceData->price_ah }}</td>

                                                @php
                                                    $Amount = 0;
                                                    $Amount = $TotalAH * $batterypriceData->price_ah;
                                                @endphp
                                                <td>{{ $Amount }}</td>
                                                @php
                                                    $Freight = 0;
                                                    $Freight = $batterypriceData->freight_age * $Amount;
                                                @endphp
                                                <td>{{ $Freight }}</td>
                                                @php

                                                    $Total_Incl_Freight = $Freight + $Amount;
                                                @endphp
                                                <td>{{ $Total_Incl_Freight }}</td>

                                                @php
                                                    $installationTotal = 0;
                                                    $installationTotal = App\Models\Battery_installation_view_sheet::where(
                                                        'itemid',
                                                        $batterypriceData->id,
                                                    )->sum('batteryinstallation_value');
                                                @endphp


                                                <td>{{ $installationTotal }}</td>
                                                @php
                                                    $CostperBank = 0;
                                                    $CostperBank = $Total_Incl_Freight + $installationTotal;
                                                @endphp

                                                <td>{{ $CostperBank }}</td>

                                                @php
                                                    $SellingPrice = 0;
                                                    $SellingPrice = round(
                                                        ($CostperBank * (1 + $batterypriceData->cash_margin / 100)) /
                                                            (1 - $batterypriceData->bg / 100),
                                                    );
                                                @endphp
                                                <td>{{ $SellingPrice }}</td>


                                                <td>{{ $batterypriceData->bidding_installtion_cost }}</td>

                                                @php
                                                    $BiddingBatteryCost = 0;
                                                    $BiddingBatteryCost = abs(
                                                        floatval($batterypriceData->bidding_installtion_cost) -
                                                            floatval($SellingPrice),
                                                    );
                                                @endphp
                                                <td>{{ $BiddingBatteryCost }}</td>


                                                <td>{{ $batterypriceData->no_of_banks }}</td>
                                                @php
                                                    $TotalAmountBattery = 0;
                                                    $TotalAmountBattery =
                                                        $batterypriceData->no_of_banks * $BiddingBatteryCost;
                                                @endphp
                                                <td>{{ $TotalAmountBattery }}</td>

                                                @php
                                                    $TotalIC = 0;
                                                    $TotalIC =
                                                        floatval($batterypriceData->no_of_banks) *
                                                        floatval($batterypriceData->bidding_installtion_cost);
                                                @endphp
                                                <td>{{ $TotalIC }}</td>
                                                @php
                                                    $TotalBatteryCost = 0;
                                                    $TotalBatteryCost =
                                                        floatval($batterypriceData->no_of_banks) *
                                                        floatval($Total_Incl_Freight);
                                                @endphp
                                                <td>{{ $TotalBatteryCost }}</td>
                                                @php
                                                    $TotalICCost = 0;
                                                    $TotalICCost =
                                                        floatval($batterypriceData->no_of_banks) *
                                                        floatval($installationTotal);
                                                @endphp
                                                <td>{{ $TotalICCost }}</td>

                                                <td>{{ $batterypriceData->old_battery_bank }}</td>
                                                @php
                                                    $Weightperbank = 0;
                                                    $Weightperbank =
                                                        floatval($batterypriceData->old_battery_bank) *
                                                        floatval($batterypriceData->cells_per_bank);
                                                @endphp
                                                <td>{{ $Weightperbank }}</td>
                                                @php
                                                    $TotalWeight = 0;
                                                    $TotalWeight =
                                                        floatval($Weightperbank) *
                                                        floatval($batterypriceData->no_of_banks);
                                                @endphp
                                                <td>{{ $TotalWeight }}</td>
                                                @php
                                                    $BuyCost = 0;
                                                    $BuyCost =
                                                        floatval($Weightperbank) * floatval($batterypriceData->buyback);
                                                @endphp
                                                <td>{{ $BuyCost }}</td>
                                                @php
                                                    $TotalBuyBack = 0;
                                                    $TotalBuyBack =
                                                        floatval($BuyCost) * floatval($batterypriceData->no_of_banks);
                                                @endphp
                                                <td>{{ $TotalBuyBack }}</td>
                                                @php
                                                    $BuybackCost = 0;
                                                    $BuybackCost =
                                                        floatval($Weightperbank) *
                                                        floatval($batterypriceData->actual_buyback);
                                                @endphp
                                                <td>{{ $BuybackCost }}</td>
                                                @php
                                                    $TotalBuyback = 0;
                                                    $TotalBuyback =
                                                        floatval($BuybackCost) *
                                                        floatval($batterypriceData->no_of_banks);
                                                @endphp
                                                <td>{{ $TotalBuyback }}</td>
                                                @php
                                                    $GSTonBattery = 0;
                                                    $GSTonBattery = round(
                                                        floatval($BiddingBatteryCost) *
                                                            floatval($batterypriceData->gst_on_battery / 100),
                                                    );
                                                @endphp
                                                <td>{{ $GSTonBattery }}</td>
                                                @php
                                                    $GSTonIC = 0;
                                                    $GSTonIC =
                                                        floatval($batterypriceData->bidding_installtion_cost) *
                                                        floatval($batterypriceData->gst_on_ic / 100);
                                                @endphp
                                                <td>{{ $GSTonIC }}</td>
                                                @php
                                                    $GSTonBuyback = 0;
                                                    $GSTonBuyback =
                                                        floatval($BuyCost) *
                                                        floatval($batterypriceData->gst_on_buyback / 100);
                                                @endphp
                                                <td>{{ $GSTonBuyback }}</td>
                                                @php
                                                    $unit = 0;
                                                    $unit =
                                                        floatval($BiddingBatteryCost) +
                                                        floatval($batterypriceData->bidding_installtion_cost) -
                                                        floatval($BuyCost);
                                                @endphp
                                                <td>{{ $unit }}</td>
                                                @php
                                                    $unitTax = 0;
                                                    $unitTax =
                                                        floatval($unit) +
                                                        floatval($GSTonBattery) +
                                                        floatval($GSTonIC) -
                                                        floatval($GSTonBuyback);
                                                @endphp
                                                <td>{{ $unitTax }}</td>
                                                @php
                                                    $basicbank = 0;
                                                    $basicbank =
                                                        floatval($unit) * floatval($batterypriceData->no_of_banks);
                                                @endphp
                                                <td>{{ $basicbank }}</td>
                                                @php
                                                    $taxinclusive = 0;
                                                    $taxinclusive =
                                                        floatval($unitTax) * floatval($batterypriceData->no_of_banks);
                                                @endphp
                                                <td>{{ $taxinclusive }}</td>
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
        <div class="row">
            <div class="col-md-12 m-auto">
                <h5>Battery Installation View</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">

                            <div class="table-responsive">
                                <button class="btn btn-primary btn-xs"
                                    onclick="exportTableToExcel('basic211', 'Battery_Installation')">
                                    Download Excel
                                </button>
                                <table id="basic211" class="table-striped dataTable border table-bordered"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.no.</th>
                                            <th>Item Name</th>
                                            <th>Model</th>
                                            <th>AH</th>
                                            <th>Cells per Bank</th>
                                            @foreach ($batteryinstallation as $batteryinstallationData)
                                                <th>
                                                    {{ $batteryinstallationData->title }}
                                                </th>
                                            @endforeach
                                            <th>Cost</th>
                                            <th>No. of Banks</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($batteryprice as $key => $batterypriceData)
                                            @php
                                                $installationTotal = 0;
                                                $installationValues = [];
                                            @endphp

                                            @foreach ($batteryinstallation as $installationData)
                                                @php
                                                    $installations = App\Models\Battery_installation_view_sheet::where(
                                                        'itemid',
                                                        $batterypriceData->id,
                                                    )
                                                        ->where('batteryinstallation_id', $installationData->id)
                                                        ->first();

                                                    $installationvalue = !empty($installations)
                                                        ? $installations->batteryinstallation_value
                                                        : 0;

                                                    $installationValues[] = $installationvalue;
                                                    $installationTotal += $installationvalue;
                                                @endphp
                                            @endforeach


                                            @if ($installationTotal > 0)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $batterypriceData->item_name }}</td>
                                                    <td>
                                                        @if (isset($batterypriceData->itemModel))
                                                            {{ $batterypriceData->itemModel->model }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $batterypriceData->ah }}</td>
                                                    <td>{{ $batterypriceData->cells_per_bank }}</td>

                                                    @foreach ($installationValues as $value)
                                                        <td>{{ $value }}</td>
                                                    @endforeach

                                                    <td>{{ $installationTotal }}</td>
                                                    <td>{{ $batterypriceData->no_of_banks }}</td>
                                                    <td>{{ $installationTotal * $batterypriceData->no_of_banks }}</td>
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
        </div>
        <div class="row">
            <div class="col-md-12 m-auto">

                <h5>Battery Accessories View</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <button class="btn btn-primary btn-xs"
                                onclick="exportTableToExcel('basic21', 'Battery_Accessories')">
                                Download Excel
                            </button>
                            <div class="table-responsive">
                                <table id="basic21" class="table-striped dataTable table-bordered border "
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.no.</th>
                                            <th>Item Name</th>
                                            <th>Model</th>
                                            <th>AH</th>
                                            <th>Cells per Bank</th>
                                            @foreach ($batteryaccessories as $batteryaccessoriesData)
                                                <th>
                                                    {{ $batteryaccessoriesData->title }}
                                                </th>
                                            @endforeach
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($batteryprice as $key => $batterypriceData)
                                            @php
                                                $installationTotal = 0;
                                                $accessoriesValues = [];
                                            @endphp

                                            @foreach ($batteryaccessories as $accessoriesData)
                                                @php
                                                    $accessories = App\Models\Battery_accessories_view_sheet::where(
                                                        'itemid',
                                                        $batterypriceData->id,
                                                    )
                                                        ->where('batteryaccessories_id', $accessoriesData->id)
                                                        ->first();

                                                    $accessoriesvalue = !empty($accessories)
                                                        ? $accessories->batteryaccessories_value
                                                        : 0;

                                                    $accessoriesValues[] = $accessoriesvalue;
                                                    $installationTotal += $accessoriesvalue;
                                                @endphp
                                            @endforeach


                                            @if ($installationTotal > 0)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $batterypriceData->item_name }}</td>
                                                    <td>
                                                        @if (isset($batterypriceData->itemModel))
                                                            {{ $batterypriceData->itemModel->model }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $batterypriceData->ah }}</td>
                                                    <td>{{ $batterypriceData->cells_per_bank }}</td>

                                                    @foreach ($accessoriesValues as $value)
                                                        <td>{{ $value }}</td>
                                                    @endforeach

                                                    <td>{{ $installationTotal }}</td>
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
        </div>
    </section>


    @push('scripts')
        <script>
            function exportTableToExcel(tableID, filename = '') {
                let tableSelect = document.getElementById(tableID);

                if (!tableSelect) {
                    alert('Table not found!');
                    return;
                }

                let clonedTable = tableSelect.cloneNode(true);
                let cells = clonedTable.getElementsByTagName("td");
                for (let i = 0; i < cells.length; i++) {
                    if (cells[i].innerText.trim() === "") {
                        cells[i].innerText = "0";
                    }
                }

                let tableHTML = clonedTable.outerHTML.replace(/ /g, '%20');
                const dataType = 'application/vnd.ms-excel';
                filename = filename ? filename + '.xls' : 'excel_data.xls';

                let downloadLink = document.createElement("a");
                document.body.appendChild(downloadLink);

                if (navigator.msSaveOrOpenBlob) {
                    let blob = new Blob(['\ufeff', tableHTML], {
                        type: dataType
                    });
                    navigator.msSaveOrOpenBlob(blob, filename);
                } else {
                    downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                    downloadLink.download = filename;
                    downloadLink.click();
                }

                document.body.removeChild(downloadLink);
            }
        </script>
    @endpush
@endsection
