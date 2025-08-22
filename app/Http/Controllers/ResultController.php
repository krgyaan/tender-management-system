<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rfq;
use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Support\Str;
use App\Models\TenderResult;
use Illuminate\Http\Request;
use app\Mail\TenderResultMail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class ResultController extends Controller
{
    public function index()
    {
        return view('tender.result');
    }

    public function getResultData(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');
        Log::info('Fetching Result Data', ['type' => $type]);

        $query = TenderInfo::query()
            ->whereNotIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1');

        // Team filtering
        if (!in_array($user->role, ['admin'])) {
            if (in_array($user->role, ['team-leader', 'coordinator'])) {
                $query->where('team', $user->team);
            } else {
                $query->where('team_member', $user->id);
            }
        } else if ($team) {
            $query->where('team', $team);
        }

        if ($type === 'pending') {
            $query->whereBetween('status', [17, 23]);
            $res_status = 'Result Awaited';
        } elseif ($type === 'won') {
            $query->where('status', 25);
            $res_status = 'Won';
        } elseif ($type === 'lost') {
            $query->where('status', 24);
            $res_status = 'Lost';
        }

        $tenders = $query->with(['bs', 'users', 'itemName', 'statuses'])->get();

        $tenders->sortBy(function ($t) {
            $date = optional($t->bs)->bid_submissions_date;
            return $date ? Carbon::parse($date) : now();
        });

        $dataTable = DataTables::of($tenders)
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
                <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('item_name.name', function ($tender) {
                return $tender->itemName->name ?? 'N/A';
            })
            ->addColumn('tender_status', function ($tender) {
                return $tender->statuses->name ?? 'N/A';
            })
            ->addColumn('emd_details', function ($tender) {
                if ($tender->emds && $tender->emds->isNotEmpty()) {
                $emd = $tender->emds->first();
                $mode = $emd->instrument_type;
            
                    switch ($mode) {
                        case 1:
                            $status = optional($emd->emdDemandDrafts->first())->action;
                            $mode = "DD";
                            break;
                        case 2:
                            $status = optional($emd->emdFdrs->first())->action;
                            $mode = "FDR";
                            break;
                        case 3:
                            $status = optional($emd->emdCheques->first())->action;
                            $mode = "Cheque";
                            break;
                        case 4:
                            $status = optional($emd->emdBgs->first())->action;
                            $mode = "BG";
                            break;
                        case 5:
                            $status = optional($emd->emdBankTransfers->first())->action;
                            $mode = "Bank Transfer";
                            break;
                        case 6:
                            $status = optional($emd->emdPayOnPortals->first())->action;
                            $mode = "Pay on Portal";
                            break;
                        default:
                            $status = 'Unknown mode';
                    }
                
                    return "$mode($status)";
                } else if ($tender->emd > 0) {
                    return "Not Requested";
                } else {
                    return "Not Applicable";
                }
            })
            ->addColumn('final_price', function ($tender) {
                return $tender->bs ? format_inr($tender->final_price) : format_inr($tender->gst_values);
            })
            ->addColumn('bid_submissions_date', function ($tender) {
                if ($tender->bs) {
                    return '<span class="d-none">' . strtotime($tender->bs->bid_submissions_date) . '</span>' .
                        date('d-m-Y', strtotime($tender->bs->bid_submissions_date)) . '<br>' .
                        (isset($tender->bs->bid_submissions_date) ? date('h:i A', strtotime($tender->bs->bid_submissions_date)) : '');
                }
                return 'Not Found';
            })
            ->addColumn('result_status', function ($tender) use ($res_status) {
                return $res_status;
            })
            ->addColumn('action', function ($tender) use ($type) {
                return view('partials.result-action', ['tender' => $tender]);
            })
            ->rawColumns(['tender_name', 'bid_submissions_date', 'action', 'emd_details'])
            ->make(true);
        return $dataTable;
    }

    public function storeTechnicalResult(Request $request)
    {
        $rules = [
            'tender_id' => 'required|exists:tender_infos,id',
            'technically_qualified' => 'required|in:yes,no',
        ];

        if ($request->technically_qualified === 'no') {
            $rules['disqualification_reason'] = 'required';
        } else {
            $rules['qualified_parties_count'] = 'nullable';
            $rules['qualified_parties_names'] = 'nullable|array';
            $rules['qualified_parties_screenshot'] = 'required|array';
        }

        $request->validate($rules);

        try {
            $data = $request->except(['qualified_parties_names', 'qualified_parties_screenshot']);

            // Handle qualified parties names
            if ($request->technically_qualified === 'yes') {
                $data['qualified_parties_names'] = json_encode($request->qualified_parties_names);
            }

            // Create uploads directory if it doesn't exist
            $uploadPath = 'uploads/tender-results';
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }

            // Handle screenshot upload
            if ($request->hasFile('qualified_parties_screenshot')) {
                $fileNames = [];
                foreach ($request->file('qualified_parties_screenshot') as $file) {
                    $fileName = 'screenshot_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $fileName);
                    $fileNames[] = $fileName;
                }
                $data['qualified_parties_screenshot'] = json_encode($fileNames);
            }

            // Create or update tender result
            TenderResult::updateOrCreate(
                ['tender_id' => $request->tender_id],
                $data
            );

            return redirect()->back()->with('success', 'Technical qualification details saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving technical qualification: ' . $e->getMessage());
        }
    }

    public function storeFinalResult(Request $request)
    {
        $rules = [
            'tender_id' => 'required|exists:tender_infos,id',
            'result' => 'required|in:won,lost',
            'l1_price' => 'required|numeric',
            'l2_price' => 'required|numeric',
            'our_price' => 'required|numeric',
            'final_result' => 'required|file'
        ];

        $request->validate($rules);

        try {
            $data = $request->except(['final_result']);

            // Create uploads directory if it doesn't exist
            $uploadPath = 'uploads/tender-results';
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }

            // Handle final result file upload
            if ($request->hasFile('final_result')) {
                $file = $request->file('final_result');
                $fileName = 'final_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $fileName);
                $data['final_result'] = $fileName;
            }

            // Update existing tender result
            TenderResult::updateOrCreate(
                ['tender_id' => $request->tender_id],
                $data
            );

            // Update tender status based on result
            $tender = TenderInfo::find($request->tender_id);
            $status = $request->result === 'won' ? 25 : 24;
            $tender->update(['status' => $status]);
            
            // send email to the team
            if ($this->sendRaResultMail($request->tender_id)) {
                Log::info('RA result email sent successfully.');
            } else {
                Log::error('Failed to send RA result email.');
            }

            return redirect()->back()->with('success', 'Final result has been saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving final result: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        try {
            $tender = TenderInfo::where('id', $id)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            $rfq = Rfq::where('tender_id', $id)->firstOrFail();

            return view('tender.result-show', compact('tender', 'rfq'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    // MAILS
    public function sendRaResultMail($id)
    {
        try {
            $result = TenderResult::where('tender_id', $id)->first();
            if (!$result) {
                return redirect()->back()->with('error', 'Result not found.');
            }
            $tender = TenderInfo::where('id', $result->tender_id)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            Log::error("Tender Result: " . json_encode($result));
            
            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }
            $admin = User::where('role', 'admin')->pluck('email')->toArray();
            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'team-leader')->where('team', $te->team)->first();
            $cc = array_merge($admin, [$cord->email]);

            $parties = json_decode($result->qualified_parties_screenshot, true);
            $finalresult = $result->final_result;

            $files = array_merge($parties, [$finalresult]);

            $data = [
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'result' => $result->result,
                'l1_price' => $result->l1_price,
                'l2_price' => $result->l2_price,
                'our_price' => $result->our_price,
                'qualified_parties_screenshot' => $result->qualified_parties_screenshot,
                'final_result_screenshot' => $result->final_result,
                'files' => $files,
            ];

            MailHelper::configureMailer($te->email, $te->app_password, $te->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to($tl->email)
                ->cc($cc)
                ->send(new TenderResultMail($data));

            if (!$mail) {
                Log::error('Failed to send RA scheduled email');
            }
            Log::info('RA scheduled email sent successfully to: ' . implode(', ', [$tl->email, $te->email]));
            return redirect()->back()->with('success', 'RA result email sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
