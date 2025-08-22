<?php

namespace App\Http\Controllers;

use App\Models\Accounts\Amc\AmcSite;
use App\Models\Accounts\Amc\AmcSiteContact;
use App\Models\Accounts\Amc\AmcServiceEngineer;
use App\Models\Accounts\Amc\AmcProduct;
use App\Models\Accounts\Amc\Amc;
use App\Models\Project;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
class AmcController extends Controller
{
    /**
     * Display a listing of the AMCs.
     */
    public function index()
    {
        $amcs = Amc::with(['project', 'sites.contacts', 'engineers', 'products.item'])->latest();

        return view('service.amc.index', compact('amcs'));
    }

    /**
     * Show the form for creating a new AMC.
     */
    public function create()
    {
        $projects = Project::all();
        $items = Item::all();
        
        return view('service.amc.create', compact('projects', 'items'));
    }

    /**
     * Store a newly created AMC in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name' => 'required|in:ac,dc',
            'project_id' => 'required|exists:projects,id',
            'sites' => 'required|array|min:1',
            'sites.*.name' => 'required|string|max:255',
            'sites.*.address' => 'required|string',
            'sites.*.map_link' => 'nullable|url',
            'sites.*.contacts' => 'required|array|min:1',
            'sites.*.contacts.*.name' => 'required|string|max:255',
            'sites.*.contacts.*.mobile' => 'required|string|max:20',
            'sites.*.contacts.*.email' => 'nullable|email',
            'sites.*.contacts.*.organization' => 'nullable|string',
            'service_frequency' => 'required|in:weekly,monthly,quarterly,yearly,custom',
            'amc_start_date' => 'required|date',
            'amc_end_date' => 'required|date|after_or_equal:amc_start_date',
            'bill_frequency' => 'required|in:monthly,quarterly,annual',
            'bill_type' => 'required|in:constant,variable',
            'constant_bill_value' => 'required_if:bill_type,constant|numeric|min:0',
            'variable_bills' => 'required_if:bill_type,variable|array|min:1',
            'variable_bills.*.date' => 'required_if:bill_type,variable|date',
            'variable_bills.*.amount' => 'required_if:bill_type,variable|numeric|min:0',
            'amc_po' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'engineers' => 'required|array|min:1',
            'engineers.*.name' => 'required|string|max:255',
            'engineers.*.mobile' => 'required|string|max:20',
            'engineers.*.email' => 'nullable|email',
            'engineers.*.organization' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.item_id' => 'required|exists:items,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Handle file upload
            $amcPoPath = null;
            if ($request->hasFile('amc_po')) {
                $amcPoPath = $request->file('amc_po')->store('amc_pos');
            }

            // Prepare variable bills data
            $variableBills = null;
            if ($request->bill_type === 'variable' && isset($request->variable_bills)) {
                $variableBills = array_map(function($bill) {
                    return [
                        'date' => $bill['date'],
                        'amount' => $bill['amount']
                    ];
                }, $request->variable_bills);
            }

            // Create AMC
            $amc = Amc::create([
                'team_name' => $request->team_name,
                'project_id' => $request->project_id,
                'service_frequency' => $request->service_frequency,
                'amc_start_date' => $request->amc_start_date,
                'amc_end_date' => $request->amc_end_date,
                'bill_frequency' => $request->bill_frequency,
                'bill_type' => $request->bill_type,
                'bill_value' => $request->bill_type === 'constant' ? $request->constant_bill_value : null,
                'variable_bills' => $variableBills,
                'amc_po_path' => $amcPoPath,
            ]);

            // Save Sites and Contacts
            foreach ($request->sites as $siteData) {
                $site = $amc->sites()->create([
                    'name' => $siteData['name'],
                    'address' => $siteData['address'],
                    'map_link' => $siteData['map_link'] ?? null,
                ]);

                // Save Contacts for this site
                foreach ($siteData['contacts'] as $contactData) {
                    $site->contacts()->create([
                        'name' => $contactData['name'],
                        'organization' => $contactData['organization'] ?? null,
                        'mobile' => $contactData['mobile'],
                        'email' => $contactData['email'] ?? null,
                    ]);
                }
            }

            // Save Service Engineers
            foreach ($request->engineers as $engineerData) {
                $amc->engineers()->create([
                    'name' => $engineerData['name'],
                    'organization' => $engineerData['organization'] ?? null,
                    'mobile' => $engineerData['mobile'],
                    'email' => $engineerData['email'] ?? null,
                ]);
            }

            // Save Products
            foreach ($request->products as $productData) {
                $amc->products()->create([
                    'item_id' => $productData['item_id'],
                    'description' => $productData['description'] ?? null,
                    'make' => $productData['make'] ?? null,
                    'model' => $productData['model'] ?? null,
                    'serial_no' => $productData['serial_no'] ?? null,
                    'quantity' => $productData['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('service.amc.index')->with('success', 'AMC created successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create AMC: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified AMC.
     */
    public function show(Amc $amc)
    {
        $amc->load([
            'project', 
            'sites.contacts', 
            'engineers', 
            'products.item'
        ]);

        return view('service.amc.show', compact('amc'));
    }

    /**
     * Show the form for editing the specified AMC.
     */
    public function edit(Amc $amc)
    {
        $projects = Project::all();
        $items = Item::all();
        
        $amc->load(['sites.contacts', 'engineers', 'products']);

        return view('service.amc.edit', compact('amc', 'projects', 'items'));
    }

    /**
     * Update the specified AMC in storage.
     */
    public function update(Request $request, Amc $amc)
    {
        $validated = $request->validate([
            'team_name' => 'required|in:ac,dc',
            'project_id' => 'required|exists:projects,id',
            'sites' => 'required|array|min:1',
            'sites.*.name' => 'required|string|max:255',
            'sites.*.address' => 'required|string',
            'sites.*.map_link' => 'nullable|url',
            'sites.*.contacts' => 'required|array|min:1',
            'sites.*.contacts.*.name' => 'required|string|max:255',
            'sites.*.contacts.*.mobile' => 'required|string|max:20',
            'sites.*.contacts.*.email' => 'nullable|email',
            'sites.*.contacts.*.organization' => 'nullable|string',
            'service_frequency' => 'required|in:weekly,monthly,quarterly,yearly,custom',
            'amc_start_date' => 'required|date',
            'amc_end_date' => 'required|date|after_or_equal:amc_start_date',
            'bill_frequency' => 'required|in:monthly,quarterly,annual',
            'bill_type' => 'required|in:constant,variable',
            'constant_bill_value' => 'required_if:bill_type,constant|numeric|min:0',
            'variable_bills' => 'required_if:bill_type,variable|array|min:1',
            'variable_bills.*.date' => 'required_if:bill_type,variable|date',
            'variable_bills.*.amount' => 'required_if:bill_type,variable|numeric|min:0',
            'amc_po' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'engineers' => 'required|array|min:1',
            'engineers.*.name' => 'required|string|max:255',
            'engineers.*.mobile' => 'required|string|max:20',
            'engineers.*.email' => 'nullable|email',
            'engineers.*.organization' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.item_id' => 'required|exists:items,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Handle file upload
            if ($request->hasFile('amc_po')) {
                // Delete old file if exists
                if ($amc->amc_po_path) {
                    Storage::delete($amc->amc_po_path);
                }
                $amcPoPath = $request->file('amc_po')->store('amc_pos');
                $amc->amc_po_path = $amcPoPath;
            }

            // Prepare variable bills data
            $variableBills = null;
            if ($request->bill_type === 'variable' && isset($request->variable_bills)) {
                $variableBills = array_map(function($bill) {
                    return [
                        'date' => $bill['date'],
                        'amount' => $bill['amount']
                    ];
                }, $request->variable_bills);
            }

            // Update AMC
            $amc->update([
                'team_name' => $request->team_name,
                'project_id' => $request->project_id,
                'service_frequency' => $request->service_frequency,
                'amc_start_date' => $request->amc_start_date,
                'amc_end_date' => $request->amc_end_date,
                'bill_frequency' => $request->bill_frequency,
                'bill_type' => $request->bill_type,
                'bill_value' => $request->bill_type === 'constant' ? $request->constant_bill_value : null,
                'variable_bills' => $variableBills,
            ]);

            // Sync Sites and Contacts
            $amc->sites()->delete(); // Delete all existing sites and contacts (cascade)
            
            foreach ($request->sites as $siteData) {
                $site = $amc->sites()->create([
                    'name' => $siteData['name'],
                    'address' => $siteData['address'],
                    'map_link' => $siteData['map_link'] ?? null,
                ]);

                foreach ($siteData['contacts'] as $contactData) {
                    $site->contacts()->create([
                        'name' => $contactData['name'],
                        'organization' => $contactData['organization'] ?? null,
                        'mobile' => $contactData['mobile'],
                        'email' => $contactData['email'] ?? null,
                    ]);
                }
            }

            // Sync Engineers
            $amc->engineers()->delete();
            foreach ($request->engineers as $engineerData) {
                $amc->engineers()->create([
                    'name' => $engineerData['name'],
                    'organization' => $engineerData['organization'] ?? null,
                    'mobile' => $engineerData['mobile'],
                    'email' => $engineerData['email'] ?? null,
                ]);
            }

            // Sync Products
            $amc->products()->delete();
            foreach ($request->products as $productData) {
                $amc->products()->create([
                    'item_id' => $productData['item_id'],
                    'description' => $productData['description'] ?? null,
                    'make' => $productData['make'] ?? null,
                    'model' => $productData['model'] ?? null,
                    'serial_no' => $productData['serial_no'] ?? null,
                    'quantity' => $productData['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('service.amc.show', $amc)->with('success', 'AMC updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update AMC: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified AMC from storage.
     */
    public function destroy(Amc $amc)
    {
        DB::beginTransaction();

        try {
            // Delete associated files
            if ($amc->amc_po_path) {
                Storage::delete($amc->amc_po_path);
            }

            $amc->delete();

            DB::commit();

            return redirect()->route('service.amc.index')->with('success', 'AMC deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete AMC: ' . $e->getMessage());
        }
    }

    public function getAmcData (Request $request, $type){
        $user = Auth::user();
        $team = $request->input('team');
        {
        try {
            if (!in_array($type, ['serviceDue', 'serviceDone'])) {
                return response()->json(['error' => 'Invalid type'], 400);
            }

        Log::info("Fetching $type Amc Data for team: $team by $user->name");

        $query = Amc::with(['sites', 'engineers', 'contacts', 'project'])
            ->select('amcs.*')
            ->leftJoin('amc_sites', 'amc_sites.amc_id', '=', 'amcs.id')
            ->leftJoin('amc_service_engineers', 'amc_service_engineers.amc_id', '=', 'amcs.id')
            ->leftJoin('projects', 'projects.id', '=', 'amcs.project_id');

        if ($request->filled('team')) {
            $query->where('amcs.team_name', $request->input('team'));
        }

        // Filter by status if needed
        if ($type === 'serviceDue') {
            $query->whereDate('next_service_due', '<=', now());
        } elseif ($type === 'serviceDone') {
            $query->whereDate('next_service_due', '>', now());
        }

        return DataTables::of($query)
            ->addColumn('site_name', function ($amc) {
                return view('partials.amc-sites', ['sites' => $amc->sites])->render();
            })
            ->addColumn('contact_details', function ($amc) {
                return view('partials.amc-contacts', ['contacts' => $amc->contacts])->render();
            })
            ->addColumn('next_service_due', function ($amc) {
                return $amc->next_service_due
                    ? Carbon::parse($amc->next_service_due)->format('d-m-Y')
                    : 'N/A';
            })
            ->addColumn('engineer_name', function ($amc) {
                return view('partials.amc-engineers', ['engineers' => $amc->engineers])->render();
            })
            ->addColumn('actions', function ($amc) {
                return view('partials.amc-actions', ['amc' => $amc])->render();
            })
            ->rawColumns(['contact_details', 'site_name','engineer_name','actions'])
            ->make(true);
    } catch (\Exception $e) {
        \Log::error('AMC Data Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load AMC data'], 500);
    }
}
    }


    public function uploadServiceReport(Request $request, $id)
{
    $request->validate(['service_report' => 'required|file|mimes:pdf,docx,jpg,png']);
    $path = $request->file('service_report')->store('amc/service_reports');
    $fileName = basename($path);


    $amc = Amc::findOrFail($id);
    $amc->service_report_path = $fileName;
    $amc->save();
    return back()->with('success', 'Service report uploaded successfully.');
}

public function uploadSignedServiceReport(Request $request, $id)
{
    $request->validate(['signed_service_report' => 'required|file|mimes:pdf,docx,jpg,png']);
    $path = $request->file('signed_service_report')->store('amc/signed_service_reports');
    $fileName = basename($path);

    $amc = Amc::findOrFail($id);
    $amc->signed_service_report_path = $fileName;
    $amc->save();

    return back()->with('success', 'Signed report uploaded successfully.');
}

public function downloadSampleService($id)
{
    $amc = Amc::findOrFail($id);

    if (!$amc->signed_service_report_path) {
        return back()->with('error', 'No service report available for this AMC.');
    }

    $filePath = 'amc/signed_service_reports/' . $amc->signed_service_report_path;

    if (!Storage::exists($filePath)) {
        abort(404, 'Signed service report not found.');
    }

    return Storage::download($filePath, 'Signed-Service-Report.pdf');
}

}