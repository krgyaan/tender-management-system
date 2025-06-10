<?php

namespace App\Http\Controllers;

use App\Models\BtTenderFee;
use App\Models\DdTenderFee;
use App\Models\PopTenderFee;
use App\Models\TenderFee;
use App\Models\TenderInfo;
use Illuminate\Http\Request;

class TenderFeeController extends Controller
{
    public $instrumentType = [
        '1' => 'Demand Draft',
        '2' => 'FDR',
        '3' => 'Cheque',
        '4' => 'BG',
        '5' => 'Bank Transfer',
        '6' => 'Pay on Portal',
    ];

    public function index()
    {
        $btTenderFeesOld = BtTenderFee::where('tender_id', '0')->get();
        $btTenderFees = BtTenderFee::where('tender_id', '!=', '0')->get();
        $ddTenderFeesOld = DdTenderFee::where('tender_id', '0')->get();
        $ddTenderFees = DdTenderFee::where('tender_id', '!=', '0')->get();
        $popTenderFeesOld = PopTenderFee::where('tender_id', '0')->get();
        $popTenderFees = PopTenderFee::where('tender_id', '!=', '0')->get();
        return view('tender-fees.index', compact('btTenderFeesOld', 'btTenderFees', 'ddTenderFeesOld', 'ddTenderFees', 'popTenderFeesOld', 'popTenderFees'));
    }

    public function create($id)
    {
        $instrumentType = $this->instrumentType;
        $tender = TenderInfo::where('id', $id)->first();
        return view('tender-fees.create-direct', compact('instrumentType', 'tender'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('TenderFeeController: store() method started.');

            $validated = $request->validate([
                "tender_no" => 'required',
                "tender_name" => 'required',
                "purpose" => 'required',
                "account_name" => 'required',
                "account_number" => 'required',
                "ifsc" => 'required',
                "amount" => 'required',
            ]);

            Log::info('TenderFeeController: store() validation passed.');

            TenderFee::create($validated);

            Log::info('TenderFeeController: store() TenderFee created.');

            return redirect()->back()->with('success', 'Tender Fee Added Successfully');

        } catch (\Throwable $th) {
            Log::error('TenderFeeController: store() exception occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function BTcreate($id = null)
    {
        $instrumentType = $this->instrumentType;
        $tender = TenderInfo::where('tender_no', base64_decode($id))->first();
        $tenders = TenderInfo::where('emd', '>', '0')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->get();
        return view('tender-fees.bt.create', compact('instrumentType', 'tender', 'tenders'));
    }

    public function BTstore(Request $request)
    {
        try {
            Log::info('TenderFeeController: BTstore() method started.');

            $request->validate([
                'tender_id' => '',
                'emd_id' => '',
                'tender_name' => '',
                'due_date' => '',
                'purpose' => '',
                'account_name' => '',
                'account_number' => '',
                'ifsc' => '',
                'amount' => '',
            ]);

            Log::info('TenderFeeController: BTstore() validation passed.');

            $btTenderFee = new BtTenderFee();
            $btTenderFee->type = $request->type ?? 'Other Than TMS';
            $btTenderFee->tender_id = $request->tender_id ?? '0';
            $btTenderFee->emd_id = $request->emd_id ?? '0';
            $btTenderFee->tender_name = $request->tender_name ?? '';
            $btTenderFee->due_date = $request->due_date_time ?? '';
            $btTenderFee->purpose = $request->purpose;
            $btTenderFee->account_name = $request->account_name;
            $btTenderFee->account_number = $request->account_number;
            $btTenderFee->ifsc = $request->ifsc;
            $btTenderFee->amount = $request->amount;
            $btTenderFee->save();

            Log::info('TenderFeeController: BTstore() TenderFee created.');

            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Added Successfully');
        } catch (\Throwable $th) {
            Log::error('TenderFeeController: BTstore() exception occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function BTedit($id)
    {
        $btTenderFee = BtTenderFee::find($id);
        return view('tender-fees.bt.edit', compact('btTenderFee'));
    }

    public function BTupdate(Request $request, $id)
    {
        try {
            $btTenderFee = BtTenderFee::find($id);
            $btTenderFee->update($request->all());
            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function Popcreate($id = null)
    {
        return view('tender-fees.pop.create');
    }

    public function Popstore(Request $request)
    {
        try {
            Log::info('TenderFeeController: Popstore() method started.');

            $request->validate([
                'tender_id' => '',
                'emd_id' => '',
                'tender_name' => 'required',
                'due_date_time' => 'required',
                'purpose' => 'required',
                'portal_name' => 'required',
                'netbanking_available' => 'required',
                'bank_debit_card' => 'required',
                'amount' => 'required',
            ]);

            Log::info('TenderFeeController: Popstore() validation passed.');

            $popTenderFee = new PopTenderFee();
            $popTenderFee->tender_id = 0;
            $popTenderFee->tender_name = $request->tender_name;
            $popTenderFee->due_date_time = $request->due_date_time;
            $popTenderFee->purpose = $request->purpose;
            $popTenderFee->portal_name = $request->portal_name;
            $popTenderFee->netbanking_available = $request->netbanking_available;
            $popTenderFee->bank_debit_card = $request->bank_debit_card;
            $popTenderFee->amount = $request->amount;
            $popTenderFee->save();

            Log::info('TenderFeeController: Popstore() TenderFee created.');

            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Added Successfully');
        } catch (\Throwable $th) {
            Log::error('TenderFeeController: Popstore() exception occurred: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function Popedit($id)
    {
        $popTenderFee = PopTenderFee::find($id);
        return view('tender-fees.pop.edit', compact('popTenderFee'));
    }

    public function Popupdate(Request $request, $id)
    {
        try {
            $popTenderFee = PopTenderFee::find($id);
            $popTenderFee->update($request->all());
            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function DDcreate($id = null)
    {
        return view('tender-fees.dd.create');
    }

    public function DDstore(Request $request)
    {
        try {
            $request->validate([
                'tender_id' => '',
                'tender_name' => 'required',
                'dd_needed_in' => 'required',
                'purpose_of_dd' => 'required',
                'in_favour_of' => 'required',
                'dd_payable_at' => 'required',
                'dd_amount' => 'required',
                'courier_address' => 'required',
                'delivery_date_time' => 'required',
            ]);
            $ddTenderFee = new DdTenderFee();
            $ddTenderFee->tender_id = 0;
            $ddTenderFee->tender_name = $request->tender_name;
            $ddTenderFee->dd_needed_in = $request->dd_needed_in;
            $ddTenderFee->purpose_of_dd = $request->purpose_of_dd;
            $ddTenderFee->in_favour_of = $request->in_favour_of;
            $ddTenderFee->dd_payable_at = $request->dd_payable_at;
            $ddTenderFee->dd_amount = $request->dd_amount;
            $ddTenderFee->courier_address = $request->courier_address;
            $ddTenderFee->delivery_date_time = $request->delivery_date_time;
            $ddTenderFee->save();
            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Added Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function DDedit($id)
    {
        $ddTenderFee = DdTenderFee::find($id);
        return view('tender-fees.dd.edit', compact('ddTenderFee'));
    }

    public function DDupdate(Request $request, $id)
    {
        try {
            $ddTenderFee = DdTenderFee::find($id);
            $ddTenderFee->update($request->all());
            return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
