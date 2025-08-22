<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorOrg;
use App\Models\VendorAcc;
use App\Models\VendorGst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Exports\VendorExport;
use Maatwebsite\Excel\Facades\Excel;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = VendorOrg::with('vendors')->get();
        return view('master.vendors', compact('vendors'));
    }

    public function store(Request $request)
    {
        Log::info('VendorController: store() method started.');

        try {
            $request->validate([
                'org' => 'required|string|max:255',
            ]);

            foreach ($request->vendor as $vendor) {
                $validator = Validator::make($vendor, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:vendors,email|max:255',
                    'mobile' => 'required|unique:vendors,mobile|max:20',
                    'address' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    Log::error('VendorController: store() validation failed: ' . $validator->errors()->first());
                    return redirect()->route('vendors.index')
                        ->with('error', 'Validation failed: ' . $validator->errors()->first());
                }
            }
            Log::info('VendorController: store() validation passed.');

            $vendorOrg = VendorOrg::firstOrCreate([
                'name' => $request->org,
            ], [
                'name' => $request->org,
            ]);

            Log::info('VendorController: store() VendorOrg created: ' . $vendorOrg->id);

            foreach ($request->vendor as $vendorData) {
                Vendor::create([
                    'name' => $vendorData['name'],
                    'org' => $vendorOrg->id,
                    'email' => $vendorData['email'],
                    'mobile' => $vendorData['mobile'],
                    'address' => $vendorData['address'],
                ]);
            }

            Log::info('VendorController: store() Vendors created: ' . count($request->vendor));

            foreach ($request->accounts as $acc) {
                $validator = Validator::make($acc, [
                    'account_name' => 'required',
                    'account_num' => 'required',
                    'account_ifsc' => 'required',
                ]);

                if ($validator->fails()) {
                    Log::error('VendorController: store() accounts validation failed: ' . $validator->errors()->first());
                    return redirect()->route('vendors.index')
                        ->with('error', 'Validation failed: ' . $validator->errors()->first());
                }
            }
            Log::info('VendorController: store() accounts validation passed.');

            foreach ($request->accounts as $account) {
                VendorAcc::create([
                    'org' => $vendorOrg->id,
                    'account_name' => $account['account_name'],
                    'account_num' => $account['account_num'],
                    'account_ifsc' => $account['account_ifsc'],
                ]);
            }

            Log::info('VendorController: store() accounts created: ' . count($request->accounts));

            foreach ($request->gsts as $gst) {
                $validator = Validator::make($gst, [
                    'gst_state' => 'required',
                    'gst_num' => 'required',
                ]);

                if ($validator->fails()) {
                    Log::error('VendorController: store() gst validation failed: ' . $validator->errors()->first());
                    return redirect()->route('vendors.index')
                        ->with('error', 'Validation failed: ' . $validator->errors()->first());
                }
            }
            Log::info('VendorController: store() gst validation passed.');

            foreach ($request->gsts as $gst) {
                VendorGst::create([
                    'org' => $vendorOrg->id,
                    'gst_state' => $gst['gst_state'],
                    'gst_num' => $gst['gst_num'],
                ]);
            }

            Log::info('VendorController: store() gst created: ' . count($request->vendor));

            return redirect()->route('vendors.index')
                ->with('success', count($request->vendor) . ' Vendors created successfully.');
        } catch (\Throwable $th) {
            Log::error('VendorController: store() exception: ' . $th->getMessage());
            return redirect()->route('vendors.index')
                ->with('error', $th->getMessage());
        }
    }

    public function edit(Request $request, $id = null)
    {
        if ($id == null) {
            return redirect()->route('vendors.index')->with('error', 'Vendor not found.');
        }

        $vendor = VendorOrg::with('accounts', 'gsts')->where('id', $id)->first();
        if (!$vendor) {
            return redirect()->route('vendors.index')->with('error', 'Vendor organization not found.');
        }
        return view('master.vendor-edit', compact('vendor'));
    }
    public function update(Request $request, int $id)
    {
        // dd($request->all());
        try {
            // Validate main organization data
            $request->validate([
                'org' => 'required|string|max:255',
            ]);

            // Find and update vendor organization
            $vendorOrg = VendorOrg::findOrFail($id);
            $vendorOrg->update([
                'name' => $request->org,
            ]);

            // Validate and update vendors
            if (!empty($request->vendor)) {
                foreach ($request->vendor as $index => $vendorData) {
                    $validator = Validator::make($vendorData, [
                        'name' => 'required|string|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|max:20',
                        'address' => 'nullable|string|max:255',
                    ]);

                    if ($validator->fails()) {
                        return redirect()->route('vendors.index')
                            ->with('error', "Vendor #{$index}: " . $validator->errors()->first());
                    }

                    Vendor::updateOrCreate(
                        ['email' => $vendorData['email'] ?? null],
                        [
                            'name' => $vendorData['name'],
                            'email' => $vendorData['email'],
                            'mobile' => $vendorData['mobile'],
                            'address' => $vendorData['address'],
                            'org' => $vendorOrg->id
                        ]
                    );
                }
            }

            // Validate and update accounts
            if (!empty($request->accounts)) {
                foreach ($request->accounts as $index => $accountData) {
                    $validator = Validator::make($accountData, [
                        'account_name' => 'required|string|max:255',
                        'account_num' => 'required|string|max:255',
                        'account_ifsc' => 'required|string|max:20',
                    ]);

                    if ($validator->fails()) {
                        return redirect()->route('vendors.index')
                            ->with('error', "Account #{$index}: " . $validator->errors()->first());
                    }

                    VendorAcc::updateOrCreate(
                        ['account_num' => $accountData['account_num'] ?? null],
                        [
                            'account_name' => $accountData['account_name'],
                            'account_num' => $accountData['account_num'],
                            'account_ifsc' => $accountData['account_ifsc'],
                            'org' => $vendorOrg->id
                        ]
                    );
                }
            }

            // Validate and update GSTs
            if (!empty($request->gsts)) {
                foreach ($request->gsts as $index => $gstData) {
                    $validator = Validator::make($gstData, [
                        'gst_state' => 'required|string|max:255',
                        'gst_num' => 'required|string|max:20',
                    ]);

                    if ($validator->fails()) {
                        return redirect()->route('vendors.index')
                            ->with('error', "GST #{$index}: " . $validator->errors()->first());
                    }

                    VendorGst::updateOrCreate(
                        ['gst_num' => $gstData['gst_num'] ?? null],
                        [
                            'gst_state' => $gstData['gst_state'],
                            'gst_num' => $gstData['gst_num'],
                            'org' => $vendorOrg->id
                        ]
                    );
                }
            }

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor organization updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Vendor Update Error: ' . $th->getMessage());
            return redirect()->route('vendors.index')
                ->with('error', 'Error updating vendor: ' . $th->getMessage());
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();
            return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('vendors.index')->with('error', $th->getMessage());
        }
    }

    public function getVendorDetails(Request $request)
    {
        $vendor = Vendor::find($request->id);
        $data = [
            'email' => $vendor->email,
            'mobile' => $vendor->mobile,
            'org' => $vendor->org,
        ];
        return response()->json($data);
    }

    public function getVendorsByOrg(Request $request)
    {
        $vendors = Vendor::where('org', $request->org_id)->get();
        return response()->json($vendors);
    }

    public function deleteAccount($id)
    {
        try {
            $account = VendorAcc::findOrFail($id);
            $account->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function deleteGst($id)
    {
        try {
            $gst = VendorGst::findOrFail($id);
            $gst->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function deleteContact($id)
    {
        try {
            $contact = Vendor::findOrFail($id);
            $contact->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
    
    public function exportToExcel()
    {
        return Excel::download(new VendorExport(), 'vendors.xlsx');
    }
}
