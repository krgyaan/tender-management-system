<?php

namespace App\Http\Controllers;

use App\Models\Emds;
use App\Models\BtTenderFee;
use App\Models\DdTenderFee;
use App\Models\PopTenderFee;
use App\Models\TenderFee;
use App\Models\TenderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $fees = [
            'btTenderFees' => BtTenderFee::with('tender')->get(),
            'ddTenderFees' => DdTenderFee::with('tender')->get(),
            'popTenderFees' => PopTenderFee::with('tender')->get(),
        ];

        if (in_array(null, $fees, true)) {
            Log::error('TenderFeeController: index() method failed. Returned null.');
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Please try again.']);
        }

        return view('tender-fees.index', $fees);
    }

    public function create($id = null)
    {
        try {
            $emd = null;
            $instrumentType = request('type');
            if (!$instrumentType && $id) {
                $query = Emds::where('id', $id);

                $emd = $query->first();

                if (!$emd) {
                    throw new \Exception('EMD not found.');
                }

                $instrumentType = $emd->instrument_type;

                $emd = Emds::where('id', $id)
                    ->when($instrumentType == '1', fn($q) => $q->with(
                        ['emdDemandDrafts', 'tender:id,tender_fees,due_date,due_time']
                    ))
                    ->when($instrumentType == '5', fn($q) => $q->with(
                        ['emdBankTransfers', 'tender:id,tender_fees,due_date,due_time']
                    ))
                    ->when($instrumentType == '6', fn($q) => $q->with(
                        ['emdPayOnPortals', 'tender:id,tender_fees,due_date,due_time']
                    ))
                    ->first();

                if (!in_array($instrumentType, ['1', '5', '6'])) {
                    throw new \Exception('Invalid BI type for tender fees.');
                }
            } else if (!in_array($instrumentType, ['1', '5', '6'])) {
                throw new \Exception('Invalid BI type.');
            }

            return view('tender-fees.create', compact('instrumentType', 'emd'));
        } catch (\Exception $e) {
            Log::error('create() exception: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function BTstore(Request $request)
    {
        try {
            Log::info('TenderFeeController: BTstore() method started.');

            $validated = $request->validate([
                'tender_id' => 'required|numeric',
                'emd_id' => 'required|numeric',
                'tender_name' => 'required|string|max:255',
                'due_date_time' => 'required|date',
                'purpose' => 'required|string|max:255',
                'account_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'ifsc' => 'required|string|max:11',
                'amount' => 'required|numeric|min:0',
            ]);

            Log::info('TenderFeeController: BTstore() validation passed.');

            $btTenderFee = BtTenderFee::create([
                'tender_id' => $request->tender_id ?? '0',
                'emd_id' => $request->emd_id ?? '0',
                'type' => $request->tender_id == '0' ? 'Other Than TMS' : 'TMS',
                'tender_name' => $validated['tender_name'],
                'due_date' => $validated['due_date_time'],
                'purpose' => $validated['purpose'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'ifsc' => $validated['ifsc'],
                'amount' => $validated['amount'],
            ]);

            Log::info('TenderFeeController: BTstore() TenderFee created.', ['id' => $btTenderFee->id]);
            return redirect()->route('tender-fees.index')->with('success', 'Bank Transfer Tender Fee Added Successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('TenderFeeController: BTstore() validation exception: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('TenderFeeController: BTstore() exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while adding the Tender Fee.')->withInput();
        }
    }

    public function DDstore(Request $request)
    {
        // dd($request->all());
        try {
            $validated = $request->validate([
                'tender_id' => 'required|numeric',
                'emd_id' => 'required|numeric',
                'tender_name' => 'required|string|max:255',
                'dd_needs' => 'required|string|in:due,24,36,48',
                'purpose_of_dd' => 'required|string|max:255',
                'in_favour_of' => 'required|string|max:255',
                'dd_payable_at' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'courier_address' => 'required|string',
                'courier_deadline' => 'required|numeric|min:1',
            ]);

            $ddTenderFee = DdTenderFee::create([
                'tender_id' => $request->tender_id ?? '0',
                'emd_id' => $request->emd_id ?? '0',
                'type' => $request->tender_id == '0' ? 'Other Than TMS' : 'TMS',
                'tender_name' => $validated['tender_name'],
                'dd_needed_in' => $validated['dd_needs'],
                'purpose_of_dd' => $validated['purpose_of_dd'],
                'in_favour_of' => $validated['in_favour_of'],
                'dd_payable_at' => $validated['dd_payable_at'],
                'dd_amount' => $validated['amount'],
                'courier_address' => $validated['courier_address'],
                'delivery_date_time' => $validated['courier_deadline'],
            ]);

            Log::info('TenderFeeController: DDstore() TenderFee created.', ['id' => $ddTenderFee->id]);
            return redirect()->route('tender-fees.index')->with('success', 'Demand Draft Tender Fee Added Successfully');
        } catch (\Exception $e) {
            Log::error('TenderFeeController: DDstore() exception: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function Popstore(Request $request)
    {
        try {
            $validated = $request->validate([
                'tender_id' => 'required|numeric',
                'emd_id' => 'required|numeric',
                'tender_name' => 'required|string|max:255',
                'due_date_time' => 'required|date',
                'purpose' => 'required|string|max:255',
                'portal_name' => 'required|string|max:255',
                'netbanking_available' => 'required|string|in:yes,no',
                'bank_debit_card' => 'required|string|in:yes,no',
                'amount' => 'required|numeric|min:0',
            ]);

            $popTenderFee = PopTenderFee::create([
                'tender_id' => $request->tender_id ?? '0',
                'emd_id' => $request->emd_id ?? '0',
                'type' => $request->tender_id == '0' ? 'Other Than TMS' : 'TMS',
                'tender_name' => $validated['tender_name'],
                'due_date_time' => $validated['due_date_time'],
                'purpose' => $validated['purpose'],
                'portal_name' => $validated['portal_name'],
                'netbanking_available' => $validated['netbanking_available'],
                'bank_debit_card' => $validated['bank_debit_card'],
                'amount' => $validated['amount'],
            ]);

            Log::info('TenderFeeController: Popstore() TenderFee created.', ['id' => $popTenderFee->id]);
            return redirect()->route('tender-fees.index')->with('success', 'Pay on Portal Tender Fee Added Successfully');
        } catch (\Exception $e) {
            Log::error('TenderFeeController: Popstore() exception: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
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

    public function tender_fee_status(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'id' => 'required|numeric',
                'status' => 'required',
                'reason' => '',
                'dd_no' => '',
                'utr' => '',
                'utr_msg' => '',
                'remark' => '',
            ]);

            $type = $request->type;

            switch ($type) {
                case 'bankTransfer':
                    // dd($request->all());
                    $btTenderFee = BtTenderFee::find($request->id);
                    $btTenderFee->update([
                        'status' => $request->status,
                        'reason' => $request->reason,
                        'utr' => $request->utr,
                        'utr_msg' => $request->utr_msg,
                        'remark' => $request->remark,
                    ]);
                    return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
                    break;
                case 'payOnPortal':
                    // dd($request->all());
                    $popTenderFee = PopTenderFee::find($request->id);
                    $popTenderFee->update([
                        'status' => $request->status,
                        'reason' => $request->reason,
                        'utr' => $request->utr,
                        'utr_msg' => $request->utr_msg,
                        'remark' => $request->remark,
                    ]);
                    return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
                    break;
                case 'demandDraft':
                    // dd($request->all());
                    $ddTenderFee = DdTenderFee::find($request->id);
                    $ddTenderFee->update([
                        'status' => $request->status,
                        'reason' => $request->reason,
                        'dd_no' => $request->dd_no,
                        'utr_msg' => $request->utr_msg,
                        'remark' => $request->remark,
                    ]);
                    return redirect()->route('tender-fees.index')->with('success', 'Tender Fee Updated Successfully');
                    break;
                default:
                    break;
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
