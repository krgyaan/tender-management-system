<?php

namespace App\Http\Controllers;

use App\Mail\TenderRejectable;
use App\Mail\TenderUpdated;
use App\Models\Item;
use App\Models\PayTerm;
use App\Models\Status;
use App\Models\TenderInfo;
use App\Models\User;
use App\Models\WorkEligible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenderInfo = TenderInfo::where('deleteStatus', '0')->orderBy('due_date', 'asc')->get();
        return view('tender.pay', compact('tenderInfo'));
    }

    public function edit(TenderInfo $tenderInfo, $id)
    {
        $tenderInfo = TenderInfo::find($id);
        $items = Item::all();
        return view('tender.info', compact('tenderInfo', 'items'));
    }

    public function update(Request $request, TenderInfo $tenderInfo, $id)
    {
        dd($request->all());
        $request->validate([
            'is_rejectable' => 'nullable|numeric',
            'reject_reason' => 'nullable|string',
            'reject_remarks' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'pbg' => 'nullable|string',
            'pbg_duration' => 'nullable|string',
            'bid_valid' => 'nullable|string',
            'comm_eval' => 'nullable|string',
            'maf_req' => 'nullable|string',
            'delivery' => 'nullable|numeric',
            'supply' => 'nullable|numeric',
            'installation' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'ldperweek' => 'nullable|numeric',
            'maxld' => 'nullable|numeric',
            'phyDocs' => 'nullable|string',
            'we[*][worktype]' => 'nullable|string',
            'we[*][value]' => 'nullable|string',
            'we[*][availablity]' => 'nullable|string',
        ]);

        try {
            $pt = PayTerm::where('tender_id', $id)->first();
            if (isset($pt)) {
                $pt->update([
                    'is_rejectable' => $request->is_rejectable,
                    'reject_reason' => $request->reject_reason,
                    'reject_remarks' => $request->reject_remarks,
                    'payment_terms' => $request->payment_terms,
                    'pbg' => $request->pbg,
                    'pbg_duration' => $request->pbg_duration,
                    'bid_valid' => $request->bid_valid,
                    'comm_eval' => $request->comm_eval,
                    'maf_req' => $request->maf_req,
                    'delivery' => $request->delivery,
                    'supply' => $request->supply,
                    'installation' => $request->installation,
                    'total' => $request->total,
                    'ldperweek' => $request->ldperweek,
                    'maxld' => $request->maxld,
                    'phyDocs' => $request->phyDocs,
                ]);
            } else {
                $pt = new PayTerm();

                if ($request->has('is_rejectable') && $request->is_rejectable == 1) {
                    $pt->tender_id = $id;
                    $pt->is_rejectable = 1;
                    $pt->reject_reason = $request->reject_reason;
                    $pt->reject_remarks = $request->reject_remarks;
                    $pt->save();
                } else {
                    $pt->is_rejectable = 0;
                    $pt->reject_reason = null;
                    $pt->reject_remarks = null;
                    $pt->tender_id = $id;
                    $pt->payment_terms = $request->payment_terms;
                    $pt->pbg = $request->pbg;
                    $pt->pbg_duration = $request->pbg_duration;
                    $pt->bid_valid = $request->bid_valid;
                    $pt->comm_eval = $request->comm_eval;
                    $pt->maf_req = $request->maf_req;
                    $pt->delivery = $request->delivery;
                    $pt->supply = $request->supply;
                    $pt->installation = $request->installation;
                    $pt->total = $request->total;
                    $pt->ldperweek = $request->ldperweek;
                    $pt->maxld = $request->maxld;
                    $pt->phyDocs = $request->phyDocs;
                    $pt->save();
                }
            }
            // last inserted id
            $last_id = $pt->id;

            if ($request->has('we')) {
                foreach ($request->we as $we) {
                    if (isset($we['id'])) {
                        $we = WorkEligible::find($we['id']);
                        $we->update([
                            'worktype' => $we['worktype'],
                            'value' => $we['value'],
                            'availablity' => $we['availablity'],
                        ]);
                    } elseif (isset($we['worktype'])) {
                        WorkEligible::create([
                            'tender_id' => $id,
                            'worktype' => $we['worktype'],
                            'value' => $we['value'],
                            'availablity' => $we['availablity'],
                        ]);
                    }
                }
            }

            $tenderInfo = TenderInfo::find($id);

            if ($request->is_rejectable == 1) {
                if ($this->sendMailRej($tenderInfo, $last_id)) {
                    return redirect()->route('pay.index')->with('success', 'Tender Info updated and Mail Sent successfully');
                } else {
                    return redirect()->route('pay.index')->with('success', 'Tender Info updated successfully');
                }
            } else {
                if ($this->sendMailAcc($tenderInfo, $last_id)) {
                    return redirect()->route('pay.index')->with('success', 'Tender Info updated and Mail Sent successfully');
                } else {
                    return redirect()->route('pay.index')->with('success', 'Tender Info updated successfully');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // ====== Rejection MAIL ======
    public function sendMailRej($tenderInfo, $last_id)
    {
        try {
            $recipientEmail = User::find($tenderInfo->team_member)->email ?? 'gyanprakashk55@gmail.com';
            $member = User::find($tenderInfo->team_member)->name ?? 'gyanprakash';
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $pt = PayTerm::where('id', $last_id)->first();
            Log::info("PT:  " . json_encode($pt));
            $data = [
                'assignee' => $member,
                'tenderNo' => $tenderInfo->tender_no,
                'remarks' => $pt->reject_remarks,
                'reason' => $pt->reject_reason,
            ];
            Log::info("TENDER:  " . json_encode($data));
            Mail::to($recipientEmail)
                ->cc([$tlMail, $adminMail])
                ->send(new TenderRejectable($data));

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("TenderRejected: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    // ====== Approval MAIL ======
    public function sendMailAcc($tenderInfo, $last_id)
    {
        try {
            $request = $tenderInfo->where('id', $last_id)->first();
            $recipientEmail = User::find($request->team_member)->email ?? 'gyanprakashk55@gmail.com';
            $member = User::find($request->team_member)->name ?? 'gyanprakash';
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            Mail::to($recipientEmail)
                ->cc([$tlMail, $adminMail])
                ->send(new TenderUpdated(
                    $request->tender_no,
                    $request->tender_name,
                    $member
                ));

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("TenderApproved: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
}
