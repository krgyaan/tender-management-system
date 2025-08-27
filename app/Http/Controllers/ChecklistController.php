<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Models\DocumentChecklist;
use App\Mail\DocumentChecklistMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Services\TimerService;
use Yajra\DataTables\DataTables;

class ChecklistController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }
    public function index()
    {
        return view('tender.checklist');
    }

    public function documentChecklist(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');

        $query = TenderInfo::with('users', 'statuses')
            ->where('deleteStatus', '0')
            ->whereNotIn('status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
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

        // Filter by Checklist status
        if ($type === 'pending') {
            $query->whereDoesntHave('checklist');
        } elseif ($type === 'submitted') {
            $query->whereHas('checklist');
        }

        // Order by due_date
        if (!$request->filled('order')) {
            $query->orderByDesc('due_date');
        }

        // Global search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('tender_name', 'like', "%{$search}%")
                    ->orWhere('tender_no', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables::of($query)
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
            <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('item_name', function ($tender) {
                return optional($tender->itemName)->name ?? '-';
            })
            ->addColumn('status', function ($tender) {
                return optional($tender->itemName)->name ?? '-';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    (isset($tender->due_time) ? date('h:i A', strtotime($tender->due_time)) : '');
            })
            ->addColumn('timer', function ($tender) {
                // You can use a partial view or just a placeholder
                return view('partials.documentChecklist-timer', ['tender' => $tender])->render();
            })
            ->addColumn('action', function ($tender) {
                return view('partials.documentChecklist-action', ['tender' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'timer', 'action'])
            ->make(true);
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
            if (!$member) {
                $member = Auth::User();
            }
            $adminMail = User::where('role', 'admin')->where('team', $member->team)->first()->email ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('role', 'team-leader')->where('team', $member->team)->first();
            $coo = User::where('role', 'coordinator')->where('team', $member->team)->first();

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

            $ccRecipients = [$adminMail];
            if ($coo && isset($coo->email)) {
                $ccRecipients[] = $coo->email;
            }

            // Send mail using the configured mailer
            $mail = Mail::mailer($mailer)->to($tl->email)
                ->cc($ccRecipients)
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
