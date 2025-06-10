<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Support\Str;
use App\Models\TenderResult;
use Illuminate\Http\Request;
use app\Mail\TenderResultMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class ResultController extends Controller
{
    public function index()
    {
        $baseConditions = function ($query) {
            $query->where('deleteStatus', 0)->where('tlStatus', 1);
        };

        $baseQuery = TenderInfo::where('deleteStatus', '0')
            ->where('tlStatus', 1);

        $awaited = (clone $baseQuery)
            ->whereBetween('status', [17, 23])
            ->get()
            ->sortBy(function ($t) {
                $date = optional($t->bs)->bid_submissions_date;
                return $date ? Carbon::parse($date) : now();
            });

        $won = TenderInfo::with(['bs'])
            ->where('status', 25)
            ->get()
            ->sortBy(function ($t) {
                $date = optional($t->bs)->bid_submissions_date;
                return $date ? Carbon::parse($date) : now();
            });

        $lost = TenderInfo::with(['bs'])
            ->where('status', 24)
            ->get()
            ->sortBy(function ($t) {
                $date = optional($t->bs)->bid_submissions_date;
                return $date ? Carbon::parse($date) : now();
            });


        return view('tender.result', compact('awaited', 'won', 'lost'));
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

        // MAILS
    public function sendRaResultMail($id)
    {
        try {
            $result = TenderResult::where('tender_id', $id)->first();
            if (!$result) {
                return redirect()->back()->with('error', 'RA not found.');
            }
            $tender = TenderInfo::where('id', $result->tender_no)->first();
            if (!$tender) {
                return redirect()->back()->with('error', 'Tender not found.');
            }

            $te = User::find($tender->team_member);
            if (!$te) {
                Log::error("❌ Tender Executive not found for tender_id: {$tender->id}");
                return back()->with('error', 'Tender Executive not found.');
            }
            $admin = User::where('role', 'admin')->pluck('email')->toArray();
            $cord = User::where('role', 'coordinator')->where('team', $te->team)->first();
            $tl = User::where('role', 'tl')->where('team', $te->team)->first();
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
                'files' => $files,
            ];

            MailHelper::configureMailer($te->email, $te->app_password, $te->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to($tl->email)
                ->cc($cc)
                ->send(new TenderResultMail($data));

            if ($mail->failures()) {
                Log::error('Failed to send RA scheduled email: ' . implode(', ', Mail::failures()));
            }
            Log::info('RA scheduled email sent successfully to: ' . implode(', ', [$tl->email, $te->email]));
            return redirect()->back()->with('success', 'RA result email sent successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
