<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Models\DocumentChecklist;
use App\Mail\DocumentChecklistMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Services\TimerService;

class ChecklistController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }
    public function index()
    {
        $pendingTenders = TenderInfo::with('users', 'statuses')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->whereDoesntHave('checklist')
            ->get();

        $submittedTenders = TenderInfo::with('users', 'statuses')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->whereHas('checklist')
            ->get();

        return view('tender.checklist', compact('pendingTenders', 'submittedTenders'));
    }

    public function store(Request $request)
    {
        try {
            $allfiles = [];
            // Handle predefined documents
            if ($request->has('check')) {
                foreach ($request->check as $chk) {
                    DocumentChecklist::create([
                        'tender_id' => $request->tender_id,
                        'document_name' => $chk,
                        'document_path' => null
                    ]);
                    $allfiles[] = $chk;
                }
            }

            // Handle custom documents
            if ($request->has('docs')) {
                foreach ($request->docs as $doc) {
                    if (is_array($doc) && isset($doc['name'])) {
                        DocumentChecklist::create([
                            'tender_id' => $request->tender_id,
                            'document_name' => $doc['name'],
                            'document_path' => null
                        ]);
                    }
                    $allfiles[] = $doc['name'];
                }
            }
            // Handle uploaded files pending...

            // Stop Timer
            $tender = TenderInfo::find($request->tender_id);

            $this->timerService->stopTimer($tender, 'document_checklist');
            if ($this->sendMail($tender, $allfiles)) {
                Log::info('Document checklist sent on mail successfully.');
                return redirect()->back()->with('success', 'Document checklist sent on mail successfully.');
            } else {
                Log::error('Error sending document checklist on mail.');
                return redirect()->back()->with('error', 'Error sending document checklist on mail.');
            }
        } catch (\Throwable $th) {
            Log::error('Error Document Checklist store: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Error saving document checklist.');
        }
    }

    public function sendMail($tender, array $files)
    {
        try {
            $member = User::find($tender->team_member);
            $adminMail = User::where('role', 'admin')->where('team', 'DC')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('role', 'team-leader')->where('team', 'DC')->first();
            $coo = User::where('role', 'coordinator')->where('team', 'DC')->first();

            $data = [
                'te' => $member->name,
                'tl' => $tl->name,
                'tenderName' => $tender->tender_name,
                'tenderNo' => $tender->tender_no,
                'documents' => $files,
            ];

            Log::info("Checklist Mail Data: " . json_encode($data));

            // Configure mailer
            MailHelper::configureMailer($member->email, $member->app_password, $member->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

            // Send mail using the configured mailer
            $mail = Mail::mailer($mailer)
                ->to('gyanprakashk55@gmail.com')
                // ->cc([$adminMail, $coo->mail])
                ->send(new DocumentChecklistMail($data));

            if ($mail) {
                Log::info("Document checklist mail sent successfully");
                return true;
            } else {
                Log::error("Document checklist mail failed to send");
                return false;
            }
        } catch (\Throwable $th) {
            Log::error("Document Checklist Mail Error: " . $th->getMessage());
            return false;
        }
    }
}
