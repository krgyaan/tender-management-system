<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Basic_detail;
use App\Models\Wo_acceptance_yes;
use App\Models\Wodetails;
use Illuminate\Support\Facades\DB;

use Crypt;
use Illuminate\Support\Facades\Log;

class ContractAgreementController extends Controller
{
    public function contract_dashboardview()
    {
        $basic = Basic_detail::with('tenderName', 'wo_details')
            ->whereHas('wo_details', fn($query) => $query->where('contract_agreement_status', '1'))
            ->get();

        return view('contract_agreement.contract_dashboardview', compact('basic'));
    }

    public function uplade_contract_agereement(Request $request)
    {
        Log::info('Upload contract agreement started.', ['request_id' => $request->id]);

        $data = Wodetails::where('id', $request->id)->first();
        if (!$data) {
            Log::error('Wodetails not found.', ['request_id' => $request->id]);
            return redirect()->back()->withErrors('Wodetails not found.');
        }

        $contractAgreement = $request->file('contract_agreement');
        if ($contractAgreement) {
            $img = time() . 'contract.' . $contractAgreement->extension();
            try {
                $contractAgreement->move(public_path('uploads/applicable'), $img);
                $data->contract_agreement = $img;
                Log::info('Contract agreement uploaded.', ['file_name' => $img]);
            } catch (\Exception $e) {
                Log::error('Error uploading contract agreement.', [
                    'error' => $e->getMessage(),
                    'file_name' => $img,
                ]);
            }
        }

        $clientSigned = $request->file('client_signed');
        if ($clientSigned) {
            $img = time() . 'signed.' . $clientSigned->extension();
            try {
                $clientSigned->move(public_path('uploads/applicable'), $img);
                $data->client_signed = $img;
                Log::info('Client signed document uploaded.', ['file_name' => $img]);
            } catch (\Exception $e) {
                Log::error('Error uploading client signed document.', [
                    'error' => $e->getMessage(),
                    'file_name' => $img,
                ]);
            }
        }

        $data->save();
        Log::info('Upload contract agreement completed successfully.', ['request_id' => $request->id]);

        return redirect()->back()->with('success', 'Contract agreement uploaded successfully.');
    }


    public function viewbuttencontract($id)
    {
        $basic = Basic_detail::with('wo_details', 'wo_acceptance_yes')
            ->findOrFail($id);

        $woDetails = $basic->wo_details;
        if ($woDetails) {
            $woData = [
                'departments' => json_decode($woDetails->departments, true),
                'name' => json_decode($woDetails->name, true),
                'designation' => json_decode($woDetails->designation, true),
                'phone' => json_decode($woDetails->phone, true),
                'email' => json_decode($woDetails->email, true),
            ];
        } else {
            $woData = [];
            Log::warning('Wodetails not found for basic detail ID.', ['basic_id' => $id]);
        }
        $woAcceptance = $basic->wo_acceptance_yes;
        if ($woAcceptance->wo_yes) {
            $changes = [
                'page' => json_decode($woAcceptance->page_no, true),
                'clause' => json_decode($woAcceptance->clause_no, true),
                'current' => json_decode($woAcceptance->current_statement, true),
                'correct' => json_decode($woAcceptance->corrected_statement, true),
            ];
        } else {
            $changes = [];
            Log::warning('Wo_acceptance_yes not found for basic detail ID.', ['basic_id' => $id]);
        }
        return view('contract_agreement.view_butten', compact('basic', 'woData', 'changes'));
    }
}
