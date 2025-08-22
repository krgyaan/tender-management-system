<?php

namespace App\Http\Controllers;

use App\Models\BatteryAccessory;
use App\Models\BatteryIc;
use App\Models\BatterySheet;
use App\Models\BatterySheetItem;
use App\Models\TenderInformation;
use App\Models\TenderInfo;
use Illuminate\Http\Request;

class PricingSheetController extends Controller
{
    public function index()
    {
        $sheets = BatterySheet::all();
        return view('pricingsheet.index', compact('sheets'));
    }

    public function create()
    {
        $tenderInfo = TenderInfo::where('deleteStatus', '0')->where('tlStatus', '1')->get();
        return view('pricingsheet.create', compact('tenderInfo'));
    }

    public function step1(Request $request)
    {
        try {
            $request->validate([
                'tender_no' => 'required',
                'sheet_type' => 'required',
            ]);
            $request->session()->put('step1', $request->only('tender_no', 'sheet_type'));

            $tenderBG = TenderInformation::where('tender_id', $request->tender_no)->first();
            $bg = $tenderBG->pbg;

            return view('pricingsheet.create-two', [
                'step1' => session('step1'),
                'sheet_type' => $request->sheet_type,
                'bg' => $bg
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $step1data = session('step1');
            $finalData = array_merge($step1data, $request->all());

            $request->validate([
                'bg' => 'required|string|max:255',
                'freight_per' => 'nullable|numeric|min:0',
                'cash_margin' => 'nullable|numeric|min:0',
                'gst_battery' => 'nullable|numeric|min:0',
                'gst_ic' => 'nullable|numeric|min:0',
                'gst_buyback' => 'nullable|numeric|min:0',
                'items' => 'nullable|array',
                'items.*.item_name' => 'nullable|string|max:255',
                'items.*.model' => 'nullable|string|max:255',
                'items.*.ah' => 'nullable|numeric|min:0',
                'items.*.cells' => 'nullable|numeric|min:0',
                'items.*.spare_cell' => 'nullable|numeric|min:0',
                'items.*.price' => 'nullable|numeric|min:0',
                'items.*.banks' => 'nullable|numeric|min:0',
            ]);

            $sheet = new BatterySheet();
            $sheet->tender_id = $finalData['tender_no'];
            $sheet->sheet_type = $finalData['sheet_type'];
            $sheet->bg = $finalData['bg'];
            $sheet->freight_per = $finalData['freight_per'];
            $sheet->cash_margin = $finalData['cash_margin'];
            $sheet->gst_battery = $finalData['gst_battery'];
            $sheet->gst_ic = $finalData['gst_ic'];
            $sheet->gst_buyback = $finalData['gst_buyback'];
            $sheet->save();

            foreach ($finalData['items'] as $item) {
                $itemData = [
                    'battery_id' => $sheet->id,
                    'item_name' => $item['item_name'],
                    'model' => $item['model'],
                    'ah' => $item['ah'],
                    'cells' => $item['cells'],
                    'spare_cell' => $item['spare_cell'],
                    'price' => $item['price'],
                    'banks' => $item['banks'],
                ];
                $sheetItem = new BatterySheetItem();
                $sheetItem->fill($itemData);
                $sheetItem->save();
            }

            $request->session()->forget('step1');

            return redirect()->route('pricingsheets.index')->with('success', 'Battery sheet created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function step2(Request $request, $id = null)
    {
        if ($request->isMethod('get')) {
            $sheet = BatterySheet::find($id);
            return view('pricingsheet.create-three', compact('sheet'));
        }

        if ($request->isMethod('post')) {
            try {
                $request->validate([
                    'id' => 'required',
                    'items.*.item_id' => 'required',
                    'items' => 'required|array',
                    'items.*.accessories.*.item' => 'required|string|max:255',
                    'items.*.accessories.*.price' => 'required|numeric|min:0',
                ]);

                foreach ($request->items as $item) {
                    $total = 0;

                    // Save each accessory related to the current item
                    foreach ($item['accessories'] as $accessory) {
                        $acc = new BatteryAccessory();
                        $acc->battery_id = $request->id;
                        $acc->item_id = $item['item_id'];
                        $acc->acc_name = $accessory['item'];
                        $acc->price = $accessory['price'];
                        $total += $accessory['price'];
                        $acc->save(); // Save the accessory
                    }

                    // Update the total price for the corresponding sheet item
                    $sheetItem = BatterySheetItem::find($item['item_id']);
                    if ($sheetItem) {
                        $sheetItem->acc_total = $total;
                        $sheetItem->save();
                    }
                }

                return redirect()->route('pricingsheets.index')->with('success', 'Battery accessories added successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function step3(Request $request, $id = null)
    {
        if ($request->isMethod('get')) {
            $sheet = BatterySheet::find($id);
            return view('pricingsheet.create-four', compact('sheet'));
        }

        if ($request->isMethod('post')) {
            try {
                $request->validate([
                    'id' => 'required',
                    'items.*.item_id' => 'required',
                    'items' => 'required|array',
                    'items.*.ic.*.item' => 'required|string|max:255',
                    'items.*.ic.*.price' => 'required|numeric|min:0',
                ]);

                foreach ($request->items as $item) {
                    $total = 0;
                    // Save each ic related to the current item
                    foreach ($item['ic'] as $ic) {
                        $ics = new BatteryIc();
                        $ics->battery_id = $request->id;
                        $ics->item_id = $item['item_id'];
                        $ics->ic_name = $ic['item'];
                        $ics->price = $ic['price'];
                        $ics->bic = $ic['bic'];
                        $total += $ic['price'];
                        $ics->save();
                    }

                    // Update the prices for the corresponding sheet item
                    $sheetItem = BatterySheetItem::find($item['item_id']);
                    if ($sheetItem) {
                        $sheetItem->ic_cost = $total;
                        $bank = $sheetItem->banks;
                        $sheetItem->ic_total = $total * $bank;
                        $sheetItem->save();
                    }
                }

                return redirect()->route('pricingsheets.index')->with('success', 'Battery I&C charges added successfully.');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
    }

    public function show(string $id)
    {
        $sheet = BatterySheet::find($id);
        if (!$sheet) {
            return redirect()->route('pricingsheets.index')->with('error', 'Battery sheet not found.');
        } else {
            $sheet->load([
                'tenderInfo',
                'items',
                'items.accessories',
                'items.ics',
            ]);

            return view('pricingsheet.show', compact('sheet'));
        }
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
