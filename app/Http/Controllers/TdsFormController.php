<?php

namespace App\Http\Controllers;

use App\Models\TdsForm;
use Illuminate\Http\Request;
use App\Models\Accounts\Tds\Tds;
use App\Models\Accounts\Tds\TdsPayment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TdsFormController extends Controller
{
     private function storeFileOrNull(Request $request, string $key, string $path = 'tds/tds-documents')
        {
        return $request->hasFile($key) ? $request->file($key)->store($path, 'public') : null;
        }

    public function index()
    {
        // Fetch all TDS records
        $tdsRecords = Tds::with('payments')->paginate(10);
   
        return view('accounts.tds.index', compact('tdsRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.tds.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tds_excel' => 'required|file|mimes:xlsx,xls',
            'tally_data_link' => 'required|url',
            'tds_challan' => 'nullable|file',
            'tds_payment_challan' => 'nullable|file',
            'tds_return' => 'nullable|file',
            'payments' => 'required|array',
            'payments.*.section' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.utr_message' => 'required|string',
            'payments.*.payment_date' => 'required|date',
        ]);

   DB::beginTransaction();

    try {
        $tdsExcelPath = $this->storeFileOrNull($request, 'tds_excel');
        $tdsChallanPath = $this->storeFileOrNull($request, 'tds_challan');
        $tdsPaymentChallanPath = $this->storeFileOrNull($request, 'tds_payment_challan');
        $tdsReturnPath = $this->storeFileOrNull($request, 'tds_return');

        $tds = Tds::create([
            'tds_excel_path' => $tdsExcelPath,
            'tally_data_link' => $request->tally_data_link,
            'tds_challan_path' => $tdsChallanPath,
            'tds_payment_challan_path' => $tdsPaymentChallanPath,
            'tds_return_path' => $tdsReturnPath,
        ]);

        foreach ($request->payments as $payment) {
            TdsPayment::create([
                'tds_id' => $tds->id,
                'section' => $payment['section'],
                'amount' => $payment['amount'],
                'utr_message' => $payment['utr_message'],
                'payment_date' => $payment['payment_date'],
            ]);
        }

        DB::commit();

        return redirect()->route('accounts.tds.index')->with('success', 'TDS record created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create TDS record.')->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Tds $tds)
    {
        $tds->load('payments');
        return view('accounts.tds.show', compact('tds'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tds $tds)
        {
            $tds->load('payments');
            // $tds_id = $id;
            // print_r($id);
            // die;
            return view('accounts.tds.edit', compact('tds'));
        }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tds $tds)
    {
        $request->validate([
            'tds_excel' => 'nullable|file|mimes:xlsx,xls',
            'tally_data_link' => 'required|url',
            'tds_challan' => 'nullable|file',
            'tds_payment_challan' => 'nullable|file',
            'tds_return' => 'nullable|file',
            'payments' => 'required|array',
            'payments.*.section' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.utr_message' => 'required|string',
            'payments.*.payment_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
                $data = ['tally_data_link' => $request->tally_data_link];

                // Files
                foreach ([
                    'tds_excel' => 'tds_excel_path',
                    'tds_challan' => 'tds_challan_path',
                    'tds_payment_challan' => 'tds_payment_challan_path',
                    'tds_return' => 'tds_return_path'
                ] as $input => $column) {
                    if ($request->hasFile($input)) {
                        if (!empty($tds->$column)) {
                            Storage::delete($tds->$column);
                        }
                        $data[$column] = $request->file($input)->store('tds/tds-documents', 'public');
                    }
                }

                $tds->update($data);

                // Replace payments
                $tds->payments()->delete();
                foreach ($request->payments as $payment) {
                    TdsPayment::create([
                        'tds_id' => $tds->id,
                        'section' => $payment['section'],
                        'amount' => $payment['amount'],
                        'utr_message' => $payment['utr_message'],
                        'payment_date' => $payment['payment_date'],
                    ]);
                }

                DB::commit();

                return redirect()->route('accounts.tds.show', $tds->id)
                                ->with('success', 'TDS record updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to update TDS record.')->withInput();
            }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tds $tds)
    {
        $storagePath = 'tds/tds-documents/';

        foreach ([
            $tds->tds_excel_path,
            $tds->tds_challan_path,
            $tds->tds_payment_challan_path,
            $tds->tds_return_path
        ] as $file) {
            if (!empty($file)) {
                $fullPath = $storagePath . $file;
                if (Storage::exists($fullPath)) {
                    Storage::delete($fullPath);
                }
            }
        }

        $tds->payments()->delete();
        $tds->delete();

        return redirect()->route('accounts.tds.index')->with('success', 'TDS record deleted successfully.');
    }
}
