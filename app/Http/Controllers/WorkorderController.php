<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wodetails;
use App\Mail\PoAcceptMail;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use App\Mail\WorkOrderMail;
use App\Models\Basic_detail;
use Illuminate\Http\Request;
use App\Services\TimerService;
use App\Models\Wo_acceptance_yes;
use Illuminate\Support\Facades\DB;
use App\Mail\AmendmentFollowupMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class WorkorderController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }
    public function basicdetailview()
    {
        $tenders = TenderInfo::with('basic_details', 'bs')
            ->whereIn('status', ['25', '26', '27', '28'])
            // ->where('tlStatus', '1')
            // ->where('deleteStatus', '0')
            ->select('id', 'tender_name')
            ->get()
            ->sortByDesc(function ($tender) {
                return optional($tender->bs)->bid_submission_date;
            });
        return view('basicdetails.basicdetailview', compact('tenders'));
    }

    public function basicdetailadd($id = null)
    {
        $tender = $id ? TenderInfo::select('id', 'tender_name')->findOrFail($id) : null;
        $basicDetail = $tender ? Basic_detail::where('tender_name_id', $id)->first() : null;

        if (!$tender) {
            $team = Auth::user()->team;
            $alltenders = TenderInfo::where('team', $team)->whereIn('status', ['25', '26', '27', '28'])->get();
        } else {
            $alltenders = [];
        }

        // Pass both $tender and $basicDetail to the view
        return view('basicdetails.basicdetailadd', compact('tender', 'basicDetail', 'alltenders'));
    }

    public function basicdetailaddpost(Request $request)
    {
        Log::info('WorkorderController::basicdetailaddpost called', $request->all());

        if ($request->id) {
            $data = Basic_detail::find($request->id);
        } else {
            $data = Basic_detail::where('tender_name_id', $request->tender_name_id)->first();
        }

        if (!$data) {
            $data = new Basic_detail();
        }

        // Assign values
        $data->tender_name_id = $request->tender_name_id;
        $data->number = $request->number;
        $data->date = $request->date;
        $data->par_gst = $request->pre_gst;
        $data->par_amt = $request->pre_amt;

        // Handle image upload
        if ($request->hasFile('image')) {
            Log::info('WorkorderController::basicdetailaddpost file received');
            $img = time() . '_basicdetails.' . $request->image->extension();
            $request->image->move(public_path('upload/basicdetails'), $img);
            $data->image = $img;
        }

        $data->ip = $request->ip();
        $data->strtotime = Carbon::now('Asia/Kolkata')->timestamp;
        $data->save();

        // Once the Basic Details form is filled 72 hours are given to fill the WO Details form.
        $tender = TenderInfo::findOrFail($request->tender_name_id);
        $this->timerService->startTimer($tender, 'wo_details', 72);

        // Once the Basic Details form is submitted, the Tender status becomes 26
        $tender->status = '26';
        $tender->save();

        Log::info('WorkorderController::basicdetailaddpost successfully created/updated.');
        return redirect()->route('basicdetailview')->with('success', 'Basic Details saved successfully.');
    }

    public function basicdetailupdate($id)
    {
        $data = Basic_detail::where('id', Crypt::decrypt($id))->first();
        $tendername = DB::table('tender_infos')->get();
        return view('basicdetails.basicdetailupdate', ['dataupdate' => $data, 'tendername' => $tendername]);
    }

    public function basicdetailupdatepost(Request $request)
    {
        $data = Basic_detail::where('id', $request->id)->first();
        $data->tender_name_id = $request->tender_name_id;
        $data->number = $request->number;
        $data->date = $request->date;
        $data->par_gst = $request->pre_gst;
        $data->par_amt = $request->pre_amt;
        if ($request->image) {
            $img = time() . '_basicdetails.' . $request->image->extension();
            $request->image->move(public_path('upload/basicdetails'), $img);
            $data->image = $img;
        }
        $data->save();

        return redirect()->route('basicdetailview')->with('success', 'Data successfully added.');
    }

    public function basicdetaildelete($id)
    {
        $id = Crypt::decrypt($id);
        $basicdelete = Basic_detail::findOrFail($id);
        $basicdelete->delete();

        return redirect()->back();
    }

    public function wodetailadd($id)
    {
        $tender = TenderInfo::with(['basic_details', 'organizations'])->findOrFail($id);

        $basicDetail = $tender->basic_details;
        $basicDetailId = $basicDetail ? $basicDetail->id : null;
        $wodetails = $basicDetailId ? Wodetails::where('basic_detail_id', $basicDetailId)->first() : null;

        return view('wodetails.wodetailadd', compact('tender', 'wodetails'));
    }

    public function wodetailaddpost(Request $request)
    {
        Log::info('WorkorderController::wodetailaddpost called', $request->all());

        // Try to find existing WO detail
        $productprice = Wodetails::where('basic_detail_id', $request->basic_detail_id)->first();
        $isInsert = false;

        if (!$productprice) {
            $productprice = new Wodetails();
            $productprice->basic_detail_id = $request->basic_detail_id;
            $isInsert = true;
        }

        // Assign values (do not change basic_detail_id on update)
        $productprice->name = json_encode($request->name);
        $productprice->organization = json_encode($request->organization);
        $productprice->departments = json_encode($request->departments);
        $productprice->phone = json_encode($request->phone);
        $productprice->email = json_encode($request->email);
        $productprice->designation = json_encode($request->designation);
        $productprice->budget = $request->budget_pre_gst;
        $productprice->max_ld = $request->max_ld;
        $productprice->ldstartdate = $request->ldstartdate;
        $productprice->maxlddate = $request->maxlddate;
        $productprice->pbg_applicable_status = $request->pbg_applicable;
        $productprice->contract_agreement_status = $request->contract_agreement;

        $productprice->ip = $request->ip();
        $productprice->strtotime = Carbon::now('Asia/Kolkata')->timestamp;

        // Handle file_applicable upload
        if ($request->hasFile('file_applicable')) {
            try {
                Log::info('WorkorderController::wodetailaddpost file_applicable present');
                $img = time() . '_applicable.' . $request->file_applicable->extension();
                Log::info('Trying to save file_applicable at: ' . public_path('upload/applicable/' . $img));
                $request->file_applicable->move(public_path('upload/applicable'), $img);
                $productprice->file_applicable = $img;
                Log::info('File_applicable saved successfully');
            } catch (\Exception $e) {
                Log::error('Failed to save file_applicable: ' . $e->getMessage());
            }
        }

        // Handle file_agreement upload
        if ($request->hasFile('file_agreement')) {
            try {
                Log::info('WorkorderController::wodetailaddpost file_agreement present');
                $img = time() . '_agreement.' . $request->file_agreement->extension();
                Log::info('Trying to save file_agreement at: ' . public_path('upload/applicable/' . $img));
                $request->file_agreement->move(public_path('upload/applicable'), $img);
                $productprice->file_agreement = $img;
                Log::info('File_agreement saved successfully');
            } catch (\Exception $e) {
                Log::error('Failed to save file_agreement: ' . $e->getMessage());
            }
        }
        $productprice->save();

        // Once the WO Details form is filled 24 hours are given to the TL to fill out the WO Acceptance form, and stop wo_details timer.
        $tender = TenderInfo::findOrFail($productprice->basic_detail->tender_name_id);
        $this->timerService->stopTimer($tender, 'wo_details');
        $this->timerService->startTimer($tender, 'wo_acceptance', 24);

        $tender->status = 27;
        $tender->save();

        // Send email notification
        if ($this->workorder_mail($productprice->basic_detail_id)) {
            Log::info('WorkorderController::wodetailaddpost email sent successfully');
            $msg = $isInsert ? 'WO Details saved successfully.' : 'WO Details updated successfully.';
            return redirect()->route('basicdetailview')->with('success', $msg);
        } else {
            Log::error('WorkorderController::wodetailaddpost failed to send email');
            return redirect()->route('basicdetailview')->with('error', 'Failed to send email.');
        }
    }
    public function wodetailupdate($id)
    {
        $datawo = Wodetails::where('id', Crypt::decrypt($id))->first();
        $data['tender_info'] = DB::table('tender_infos')->get();
        $basic = Basic_detail::where('status', '1')->get();

        $datawo->organization = json_decode($datawo->organization, true);
        $datawo->departments = json_decode($datawo->departments, true);
        $datawo->name = json_decode($datawo->name, true);
        $datawo->designation = json_decode($datawo->designation, true);
        $datawo->phone = json_decode($datawo->phone, true);
        $datawo->email = json_decode($datawo->email, true);
        return view('wodetails.wodetailupdate', $data, ['basic' => $basic, 'wodetails' => $datawo]);
    }

    public function wodetailupdatepost(Request $request)
    {
        $productprice = Wodetails::where('id', $request->id)->first();
        $productprice->name = json_encode($request->name);
        $productprice->organization = json_encode($request->organization);
        $productprice->departments = json_encode($request->departments);
        $productprice->phone = json_encode($request->phone);
        $productprice->email = json_encode($request->email);
        $productprice->designation = json_encode($request->designation);

        $productprice->par_gst = $request->par_gst;
        $productprice->max_ld = $request->max_ld;
        $productprice->ldstartdate = $request->ldstartdate;
        $productprice->maxlddate = $request->maxlddate;
        $productprice->pbg_applicable_status = $request->pbg_applicable;
        $productprice->contract_agreement_status = $request->contract_agreement;

        if ($request->file_applicable) {
            $img = time() . 'applicable.' . $request->file_applicable->extension();
            $request->file_applicable->move(public_path('upload/applicable'), $img);
            $productprice->file_applicable = $img;
        }
        if ($request->file_agreement) {
            $img = time() . 'agreement.' . $request->file_agreement->extension();
            $request->file_agreement->move(public_path('upload/applicable'), $img);
            $productprice->file_agreement = $img;
        }
        $productprice->save();
        return redirect()->route('basicdetailview')->with('success', 'Data successfully Update.');
    }

    public function wodetaildelete($id)
    {
        $id = Crypt::decrypt($id);
        $wodetails = Wodetails::findOrFail($id);
        $wodetails->delete();
        return redirect()->back();
    }

    public function woacceptanceview()
    {
        return view('wodetails.woacceptanceview');
    }
    public function woacceptanceform_mail()
    {
        return view('wodetails.woacceptanceform_mail');
    }

    public function woacceptanceform($id)
    {
        $basic = Basic_detail::where('id', $id)->first();
        $woacc = Wo_acceptance_yes::where('basic_detail_id', $id)->first();
        return view('wodetails.woacceptanceform', compact('basic', 'woacc'));
    }

    public function woacceptanceformpost(Request $request)
    {
        Log::info('WO Acceptance Form Post Request: ', $request->all());

        // Try to find an existing record
        $data = Wo_acceptance_yes::where('basic_detail_id', $request->basic_detail_id)->first();

        if (!$data) {
            $data = new Wo_acceptance_yes();
            $data->basic_detail_id = $request->basic_detail_id;
        }

        // Common fields
        $data->ip = $request->ip();
        $data->strtotime = Carbon::now('Asia/Kolkata')->timestamp;

        // If amendment is needed
        if ($request->amendment_needed == 1) {
            Log::info('Amendment needed: Yes');

            $data->page_no = json_encode($request->page_no);
            $data->clause_no = json_encode($request->clause_no);
            $data->current_statement = json_encode($request->current_statement);
            $data->corrected_statement = json_encode($request->corrected_statement);
            $data->followup_frequency = $request->followup_frequency;

            // Clear previous fields if updating an old record
            $data->accepted_initiate = null;
            $data->accepted_signed = null;
        } elseif ($request->amendment_needed == 0) {
            Log::info('Amendment needed: No');

            $data->accepted_initiate = $request->accepted_initiate;

            if ($request->hasFile('accepted_signed')) {
                Log::info('Accepted signed file received');
                $img = time() . '_accepted_signed.' . $request->accepted_signed->extension();
                $request->accepted_signed->move(public_path('upload/acceptance'), $img);
                $data->accepted_signed = $img;
            }

            // Clear previous fields if updating an old record
            $data->page_no = null;
            $data->clause_no = null;
            $data->current_statement = null;
            $data->corrected_statement = null;
            $data->followup_frequency = null;
        } else {
            Log::info('Amendment not specified');
            return redirect()->back()->with('error', 'Amendment selection is required.');
        }
        $data->save();

        // Once the WO Acceptance form is filled, and the WO Amendment needed is NO,  72 hours are given to Initiate the Kick-Off Meeting.
        if ($request->amendment_needed == 0) {
            $tender = TenderInfo::findOrFail($data->basic_detail->tender_name_id);
            $this->timerService->stopTimer($tender, stage: 'wo_acceptance');
            $this->timerService->startTimer($tender, 'kickoff_meeting', 72);
        }

        // if Amendment needed then senr amendment_mail else send acceptance_mail
        if ($request->amendment_needed == 1) {
            Log::info('Sending amendment mail');
            if (!$this->sendAmendmentMail($data->basic_detail_id)) {
                Log::error('Failed to send acceptance mail');
                return redirect()->back()->with('error', 'Failed to send acceptance email.');
            }
        } else {
            Log::info('Sending acceptance mail');
            if (!$this->sendAcceptanceMail($data->basic_detail_id)) {
                Log::error('Failed to send acceptance mail');
                return redirect()->back()->with('error', 'Failed to send acceptance email.');
            }
        }

        Log::info('WO Acceptance Form: Data saved successfully');
        return redirect()->route('basicdetailview')->with('success', 'WO Acceptance Form: Data saved successfully');
    }

    public function woupdate($id)
    {
        $basic_data = Basic_detail::where('id', $id)->first();
        return view('wodetails.woupdate', ['basic_data' => $basic_data]);
    }

    public function woupdate_post(Request $request)
    {
        Log::info('WorkorderController::woupdate_post called', $request->all());

        // Find or create entry
        $data = Basic_detail::firstOrNew(['id' => $request->id]);

        // Handle LO GEM Image
        if ($request->hasFile('lo_gem_img')) {
            Log::info('LO GEM image file received');
            $img = time() . '_lo_gem.' . $request->lo_gem_img->extension();
            $request->lo_gem_img->move(public_path('upload/basicdetails'), $img);
            $data->lo_gem_img = $img;
        } else {
            $data->lo_gem_img = $data->image;
        }

        // Handle FOA SAP Image
        if ($request->hasFile('foa_sap_img')) {
            Log::info('FOA SAP image file received');
            $img = time() . '_foa_sap.' . $request->foa_sap_img->extension();
            $request->foa_sap_img->move(public_path('upload/basicdetails'), $img);
            $data->foa_sap_image = $img;
        }

        $data->save();

        Log::info('WorkorderController::woupdate_post data saved successfully');

        return redirect()->route('basicdetailview')->with('success', 'Update successfully added.');
    }

    public function wodashboardview()
    {
        $data = Wodetails::where('status', '1')->get();
        $basic_data = Basic_detail::where('status', '1')->get();
        $tendername = DB::table('tender_infos')->get();

        return view('wodetails.wodashboardview', ['wo_data' => $data, 'basic_data' => $basic_data, 'tendername' => $tendername]);
    }

    public function woviewbuttenfoa($id)
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
        return view('wodetails.woviewbuttenfoa', compact('basic', 'woData'));
    }

    // MAILS

    public function workorder_mail($id)
    {
        Log::info('WorkorderController::workorder_mail called', ['id' => $id]);

        $basic = Basic_detail::findOrFail($id);
        $tender = TenderInfo::with('organizations')->findOrFail($basic->tender_name_id);

        $user = $tender->users ?? Auth::user();
        $data = [
            'id' => $id,
            'organization_name' => $tender->organizations->name ?? 'Default Organization',
            'tender_name' => $tender->tender_name ?? 'Default Tender Name',
            'wo_dashboard_link' => route('basicdetailview'),
            'te_name' => $user->name ?? '',
            'te_mob' => $user->mobile ?? '',
            'te_mail' => $user->email ?? '',
        ];

        Log::info('WorkorderController::workorder_mail data prepared', ['data' => $data]);

        $to = User::where('role', 'team-leader')->where('team', $user->team)->pluck('email')->toArray();
        $cc = User::where('role', 'admin')->pluck('email')->toArray();

        Log::info('WorkorderController::workorder_mail to and cc prepared', ['to' => $to, 'cc' => $cc]);

        MailHelper::configureMailer($user->email, $user->app_password, $user->name);
        $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

        $mail = Mail::mailer($mailer)
            ->to($to)
            ->cc($cc)
            ->send(new WorkOrderMail($data));

        if ($mail) {
            Log::info('WorkorderController::workorder_mail email sent successfully', ['to' => $to, 'cc' => $cc]);
            return true;
        } else {
            Log::error('WorkorderController::workorder_mail failed to send email');
            return false;
        }
    }

    public function sendAmendmentMail($id)
    {
        Log::info('WorkorderController::sendAmendmentMail called', ['id' => $id]);

        $basic = Basic_detail::findOrFail($id);
        $tender = TenderInfo::with('organizations')->findOrFail($basic->tender_name_id);
        $woDetails = $basic->wo_details;
        $to = [];

        if ($woDetails) {
            $todata = [
                'email' => json_decode($woDetails->email, true),
                'name' => json_decode($woDetails->name, true),
            ];
        } else {
            Log::warning('Wodetails not found for basic detail ID.', ['basic_id' => $id]);
        }

        $user = $tender->users ?? Auth::user();
        $data = [
            'id' => $id,
            'organization_name' => $tender->organizations->name ?? 'Default Organization',
            'tender_name' => $tender->tender_name ?? 'Default Tender Name',
            'wo_no' => $basic->number ?? '',
            'wo_date' => $basic->date ?? '',
            'te_name' => $user->name ?? '',
            'te_mob' => $user->mobile ?? '',
            'te_mail' => $user->email ?? '',
        ];

        if (isset($todata['email']) && count($todata['email'])) {
            foreach ($todata['email'] as $index => $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $to[] = $email;
                } else {
                    Log::warning('Invalid email found in Wodetails email field.', ['email' => $email]);
                }
                $data['name'] = $todata['name'][$index] ?? 'Default Name';
            }

            Log::info('WorkorderController::sendAmendmentMail data prepared', ['data' => $data]);

            $cc = User::where('role', 'admin')->pluck('email')->toArray();

            Log::info('WorkorderController::sendAmendmentMail to and cc prepared', ['to' => $to, 'cc' => $cc]);

            MailHelper::configureMailer($user->email, $user->app_password, $user->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            $mail = Mail::mailer($mailer)
                ->to($to)
                ->cc($cc)
                ->send(new AmendmentFollowupMail($data));

            if ($mail) {
                Log::info('WorkorderController::sendAmendmentMail email sent successfully', ['to' => $to, 'cc' => $cc]);
                return true;
            } else {
                Log::error('WorkorderController::sendAmendmentMail failed to send email');
                return false;
            }
        }
    }

    public function sendAcceptanceMail($id)
    {
        set_time_limit(0);
        Log::info('WorkorderController::sendAcceptanceMail called', ['id' => $id]);

        $basic = Basic_detail::findOrFail($id);
        $wo = $basic->wo_acceptance_yes;
        $tender = TenderInfo::with('organizations')->findOrFail($basic->tender_name_id);

        $woDetails = $basic->wo_details;

        $to = [];
        if ($woDetails && is_array($emails = json_decode($woDetails->email, true)) && count($emails)) {
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $to[] = $email;
                } else {
                    Log::warning('Invalid email found in Wodetails email field.', ['email' => $email]);
                }
            }
        } else {
            Log::warning('Wodetails email field is not an array or is empty.', ['email' => $woDetails?->email]);
        }

        $user = $tender->users ?? Auth::user();
        $data = [
            'id' => $id,
            'is_contract' => $basic->contract_agreement_status ?? '',
            'is_pbg' => $basic->pbg_applicable_status ?? '',
            'number' => $basic->number ?? '',
            'date' => $basic->date ?? '',
            'te_name' => $user->name ?? '',
            'te_mob' => $user->mobile ?? '',
            'te_mail' => $user->email ?? '',
            'contract' => $basic->file_applicable,
            'pbg' => $basic->file_agreement,
            'signed_wo' => $wo->accepted_signed ?? '',
        ];

        Log::info('WorkorderController::sendAcceptanceMail data prepared', ['data' => $data]);

        $cc = User::where('role', 'admin')->pluck('email')->toArray();

        Log::info('WorkorderController::sendAcceptanceMail to and cc prepared', ['to' => $to, 'cc' => $cc]);

        MailHelper::configureMailer($user->email, $user->app_password, $user->name);
        $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
        Config::set('mail.mailers.smtp.timeout', 120);

        $mail = Mail::mailer($mailer)
            ->to($to)
            ->cc($cc)
            ->send(new PoAcceptMail($data));

        if ($mail) {
            Log::info('WorkorderController::sendAcceptanceMail email sent successfully');
            return true;
        } else {
            Log::error('WorkorderController::sendAcceptanceMail failed to send email');
            return false;
        }
    }
}
