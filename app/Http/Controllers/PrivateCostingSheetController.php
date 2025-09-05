<?php

namespace App\Http\Controllers;

use Google\Client;
use App\Models\Enquiry;
use App\Models\VendorOrg;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\HandlesGoogleSheet;
use Illuminate\Support\Facades\DB;
use App\Models\PrivateCostingSheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tbl_google_access_token;

class PrivateCostingSheetController extends Controller
{
    use HandlesGoogleSheet;

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'id' => 'nullable|exists:enquiries,id',
        ]);

        try {
            $title = $request->input('title');
            $end_id = $request->input('id');

            $parentFolder = '1t3zhQjJRIWXgu2pS4JnIr-1mTm4wllaL';

            $this->setGoogleRedirectUri(route('private-costing-sheet.googleSheetsCallback'));

            $sheetData = $this->createGoogleSheet($title, '', $parentFolder);

            // Handle OAuth redirect
            if (is_array($sheetData) && isset($sheetData['status']) && $sheetData['status'] === 'redirect') {
                session([
                    'pending_private_sheet' => [
                        'title'     => $title,
                        'folderId'  => $parentFolder,
                        'enquiry_id' => $end_id
                    ]
                ]);
                return redirect()->away($sheetData['auth_url']);
            }

            if (!is_array($sheetData) || !isset($sheetData['status']) || $sheetData['status'] !== true) {
                $msg = is_array($sheetData) && isset($sheetData['message']) ? $sheetData['message'] : 'Failed to create private costing sheet. Please check your Google integration.';
                return redirect()->route('enquiries.index')->with('error', $msg);
            }

            // Store record in DB
            $privateSheet = new PrivateCostingSheet();
            $privateSheet->title = $title;
            $privateSheet->enquiry_id = $end_id;
            $privateSheet->sheet_url = $sheetData['sheet_url'];
            $privateSheet->prepared_by = Auth::user()->id;
            $privateSheet->save();

            return redirect()->back()->with('success', 'Private costing sheet created successfully.');
        } catch (\Exception $e) {
            Log::error('PrivateSheetCreateError: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create private sheet: ' . $e->getMessage());
        }
    }

    public function googleSheetsCallback(Request $request)
    {
        Log::info('PrivateCostingSheet Google OAuth Callback hit', $request->all());

        if ($request->has('error')) {
            Log::info('PrivateCostingSheet Google OAuth Callback Error: ', ['error' => $request->error]);
            return redirect()->route('enquiries.index')->with('error', 'Google authentication failed.');
        }
        if (!$request->has('code')) {
            Log::info('PrivateCostingSheet Google OAuth Callback Error: No code received.');
            return redirect()->route('enquiries.index')->with('error', 'Invalid Google authentication response.');
        }

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->setGoogleRedirectUri(route('private-costing-sheet.googleSheetsCallback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        try {
            $tokenData = $client->fetchAccessTokenWithAuthCode($request->code);
            if (isset($tokenData['error'])) {
                Log::info('PrivateCostingSheet Google OAuth Callback error: ', ['error' => json_encode($tokenData['error'])]);
                return redirect()->route('enquiries.index')->with('error', 'Google authentication failed.');
            }

            $accessToken  = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;

            if (!$accessToken) {
                Log::info('PrivateCostingSheet Google OAuth Callback error: Access token not found.');
                return redirect()->route('enquiries.index')->with('error', 'Google authentication failed.');
            }

            // Save token to DB
            Tbl_google_access_token::updateOrCreate(
                ['userid' => auth()->id()],
                [
                    'access_token'  => json_encode($tokenData),
                    'refresh_token' => $refreshToken,
                    'expires_in'    => $tokenData['expires_in'] ?? null,
                    'token_type'    => $tokenData['token_type'] ?? null,
                    'scope'         => $tokenData['scope'] ?? null,
                    'updated_at'    => now(),
                    'ip'            => request()->ip(),
                ]
            );

            // Resume pending sheet creation if exists
            if (session()->has('pending_private_sheet')) {
                $pending = session()->pull('pending_private_sheet');
                $sheetData = $this->createGoogleSheet($pending['title'], '', $pending['folderId']);

                if (!is_array($sheetData) || !isset($sheetData['status']) || $sheetData['status'] !== true) {
                    $msg = is_array($sheetData) && isset($sheetData['message']) ? $sheetData['message'] : 'Failed to create private costing sheet after authentication.';
                    return redirect()->route('enquiries.index')->with('error', $msg);
                }

                // Store record in DB
                $privateSheet = new PrivateCostingSheet();
                $privateSheet->title = $pending['title'];
                $privateSheet->enquiry_id = $pending['enquiry_id'] ?? null;
                $privateSheet->sheet_url = $sheetData['sheet_url'];
                $privateSheet->prepared_by = Auth::user()->id;
                $privateSheet->save();

                return redirect()->route('enquiries.index')->with('success', 'Private costing sheet created successfully.');
            }

            return redirect()->route('enquiries.index')->with('success', 'Google Sheets connected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('enquiries.index')->with('error', 'Something went wrong during Google authentication.');
        }
    }
    // Submit costing sheet
    public function submitCosting(Request $request)
    {
        $validated = $request->validate([
            'costing_id' => 'required|exists:enquiries,id',
            'final_price' => 'required|numeric|min:0',
            'receipt_pre_gst' => 'required|numeric|min:0',
            'budget_pre_gst' => 'required|numeric|min:0',
            'gross_margin' => 'required|numeric',
            'costing_remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update costing sheet
            $costing = PrivateCostingSheet::where('id', $validated['costing_id'])->update([
                'final_price' => $validated['final_price'],
                'receipt_pre_gst' => $validated['receipt_pre_gst'],
                'budget_pre_gst' => $validated['budget_pre_gst'],
                'gross_margin' => $validated['gross_margin'],
                'remarks' => $validated['costing_remarks'],
            ]);

            DB::commit();
            Log::info('Costing Sheet Submit Success', ['sheet' => $costing]);
            return back()->with('success', 'Costing sheet submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CostingSheetSubmitError: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit costing sheet: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $sheet = PrivateCostingSheet::findOrFail($id);
        $enquiry = $sheet->enquiry;
        $lead = $enquiry->lead;
        return view('crm.enquiry.costing-view', compact('lead', 'enquiry'));
    }


    public function approvalSheet()
    {
        $oems = VendorOrg::all();
        return view('crm.enquiry.costing-approval', compact('oems'));
    }

    public function getCostingSheet(Request $request, $type)
    {
        Log::info("Starting with type=$type");

        $query = Enquiry::with('creator')
            ->whereHas('costingSheets', function ($q) {
                $q->whereNotNull('final_price');
            });


        // Pending or Submitted logic
        if ($type === 'pending') {
            $query->whereHas('costingSheets', function ($q) {
                $q->where('status', '');
            });
        } elseif ($type === 'submitted') {
            $query->whereHas('costingSheets', function ($q) {
                $q->where('status', 'Approved');
            });
        } elseif ($type === 'rejected') {
            $query->whereHas('costingSheets', function ($q) {
                $q->where('status', 'Rejected/Redo');
            });
        }
        // dd($query->toSql());

        $query->orderByDesc('id');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('enq_name', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('organization', 'like', "%{$search}%")
                            ->orWhereHas('creator', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('lead', function ($sq) use ($search) {
                                $sq->where('company_name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addColumn('enq_num', fn($enq) => "ENQ00{$enq->id}")
            ->addColumn('enq_name', fn($enq) => $enq->enq_name)
            ->addColumn('bd_lead', fn($enq) => $enq?->lead?->bd_lead?->name ?? 'N/A')
            ->addColumn('company_name', fn($enq) => $enq->lead->company_name)
            ->addColumn('organization', fn($enq) => $enq->organisation->name)
            ->addColumn('approx_value', fn($enq) => format_inr($enq->approx_value))
            ->addColumn('final_price', fn($enq) => format_inr(optional($enq->costingSheets)->final_price) ?? '-')
            ->addColumn('budget', fn($enq) => format_inr(optional($enq->costingSheets)->budget_pre_gst) ?? '-')
            ->addColumn('gross_margin', function ($enq) {
                return optional($enq->costingSheets)->gross_margin ? optional($enq->costingSheets)->gross_margin . '%' : '-';
            })
            ->addColumn('te_name', fn($enq) => $enq->creator->name)
            ->addColumn('status', fn($enq) => $enq->costingSheets->status === '' ? 'Approval Pending' : $enq->costingSheets->status)
            ->addColumn('timer', function ($enq) use ($type) {
                return 'timer';
            })
            ->addColumn('action', function ($enq) use ($type) {
                return view('partials.pvt-costing-approval-action', ['sheet' => $enq->costingSheets])->render();
            })
            ->rawColumns(['enq_num', 'timer', 'action'])
            ->make(true);
    }

    public function approvalSheetDetail(Request $request, $id)
    {
        $validated = $request->validate([
            'costing_status' => 'required|in:Approved,Rejected/Redo',
            'final_price' => 'nullable|numeric|min:0',
            'receipt' => 'nullable|numeric|min:0',
            'budget' => 'nullable|numeric|min:0',
            'gross_margin' => 'nullable|numeric|min:0|max:100',
            'costing_remarks' => 'nullable|string|max:1000',
            'oem' => 'nullable|array',
            'oem.*' => 'nullable',
        ]);
        try {
            DB::beginTransaction();

            $sheet = PrivateCostingSheet::findOrFail($id);

            // Basic fields updated for both Approved and Rejected/Redo
            $sheet->remarks = $validated['costing_remarks'] ?? null;

            if ($validated['costing_status'] === 'Approved') {
                $sheet->final_price = $validated['final_price'];
                $sheet->receipt_pre_gst = $validated['receipt'];
                $sheet->budget_pre_gst = $validated['budget'];
                $sheet->gross_margin = $validated['gross_margin'];
            } else {
                // If status is "Rejected/Redo", nullify approval-related fields
                $sheet->final_price = null;
                $sheet->receipt_pre_gst = null;
                $sheet->budget_pre_gst = null;
                $sheet->gross_margin = null;
            }

            // You can also update status if needed (e.g., if there's a 'status' column)
            $sheet->status = $validated['costing_status'];

            $sheet->save();

            // Sync OEMs if your model has a relationship like: oems()
            if (method_exists($sheet, 'oems') && isset($validated['oem'])) {
                $sheet->oems()->sync($validated['oem']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Costing sheet updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update costing sheet. ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
