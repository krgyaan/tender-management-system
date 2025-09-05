<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google\Client;
use App\Models\User;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Services\TimerService;
use App\Models\Tbl_googleapikey;
use Yajra\DataTables\DataTables;
use App\Traits\HandlesGoogleSheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\Tbl_google_access_token;
use App\Mail\CostingApprovalRequestMail;

class GoogletoolController extends Controller
{
    use HandlesGoogleSheet;
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function googlesheet(Request $request)
    {
        return view('googletool.sheets');
    }

    public function getCostingSheet(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');
        Log::info("getCostingSheet: Starting with type=$type and team=$team");

        $query = TenderInfo::with(['users', 'statuses', 'sheet'])
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

        // Pending or Submitted logic
        if ($type === 'pending') {
            $query->where(function ($q) {
                $q->whereHas('sheet', function ($q2) {
                    $q2->whereNull('final_price');
                })->orDoesntHave('sheet');
            });
        } elseif ($type === 'submitted') {
            $query->whereHas('sheet', function ($q) {
                $q->whereNotNull('final_price');
            });
        }

        // $query->orderByDesc('due_date');
        // Order by due_date
        if (!$request->filled('order')) {
            $query->orderByDesc('due_date');
        }

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('tender_name', 'like', "%{$search}%")
                            ->orWhere('tender_no', 'like', "%{$search}%")
                            ->orWhere('due_date', 'like', "%{$search}%")
                            ->orWhereHas('users', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('statuses', function ($sq) use ($search) {
                                $sq->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
            <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    (isset($tender->due_time) ? date('h:i A', strtotime($tender->due_time)) : '');
            })
            ->addColumn('emd', function ($tender) {
                return format_inr($tender->emd);
            })
            ->addColumn('tender_value', function ($tender) {
                return format_inr($tender->gst_values);
            })
            ->addColumn('final_price', function ($tender) {
                return format_inr(optional($tender->sheet)->final_price) ?? '-';
            })
            ->addColumn('budget', function ($tender) {
                return format_inr(optional($tender->sheet)->budget) ?? '-';
            })
            ->addColumn('gross_margin', function ($tender) {
                return (optional($tender->sheet)->gross_margin ? optional($tender->sheet)->gross_margin . '%' : '-');
            })
            ->addColumn('status', function ($tender) {
                return $tender->statuses ? $tender->statuses->name : '-';
            })
            ->addColumn('timer', function ($tender) use ($type) {
                return view('partials.sheet-timer', ['tender' => $tender])->render();
            })
            ->addColumn('action', function ($tender) use ($type) {
                return view('partials.sheet-action', ['tender' => $tender])->render();
            })
            ->rawColumns(['tender_name', 'due_date', 'timer', 'action'])
            ->make(true);
    }

    public function googletoolssave(Request $request)
    {
        Log::info('Starting for user: ' . Auth::user()->name);

        $title = $request->title;
        if (!$title) {
            return redirect()->route('googlesheet')->with('error', 'Sheet title is required.');
        }

        try {
            $parentFolder = match (Auth::user()->team) {
                'DC' => '1GTKETwOnO29y-XbxCMPjhPmsjs7rRS_I',
                'AC' => '1o_8fZssZ9aVO0HNLQTuQKIrsciGpHQ3g',
            };

            $sheetData = $this->createGoogleSheet($title, '', $parentFolder);

            // Handle OAuth redirect
            if ($sheetData['status'] === 'redirect') {
                session([
                    'pending_sheet' => [
                        'title'     => $title,
                        'folderId'  => $parentFolder,
                        'tenderid'  => $request->TenderInfo
                    ]
                ]);
                return redirect()->away($sheetData['auth_url']);
            }

            if ($sheetData['status'] !== true) {
                return redirect()->route('googlesheet')->with('error', $sheetData['message']);
            }

            // Continue with your database logic using the returned data
            $user = new Tbl_googleapikey();
            $user->staffid = Auth::id();
            $user->driveid = $sheetData['sheet_url'];
            $user->title = $title;
            $user->type = 'doc';
            $user->created_at = time();
            $user->updated_at = time();
            $user->description = '';
            $user->tenderid = $request->TenderInfo;
            $user->save();

            Log::info('Sheet created and saved to database successfully');
            return redirect()->route('googlesheet')->with('success', 'Costing sheet created successfully and moved to team folder.');
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return redirect()->route('googlesheet')->with('error', 'Failed to create costing sheet: ' . $e->getMessage());
        }
    }

    public function connectGoogle()
    {
        $client = new Client();
        $client->setApplicationName('Your App Name');
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([
            \Google\Service\Sheets::SPREADSHEETS,
            \Google\Service\Drive::DRIVE
        ]);

        $redirectUri = config('app.url') . 'admin/google/sheets/callback';
        $client->setRedirectUri($redirectUri);

        // Debug logging
        Log::info('OAuth Redirect URI set to: ' . $redirectUri);
        Log::info('APP_URL from config: ' . config('app.url'));

        $authUrl = $client->createAuthUrl();
        Log::info('Generated auth URL: ' . $authUrl);

        return redirect()->away($authUrl);
    }

    public function googleSheetsCallback(Request $request)
    {
        Log::info('Google OAuth Callback hit', $request->all());

        if ($request->has('error')) {
            return redirect()->route('googlesheet')->with('error', 'Google authentication failed.');
        }
        if (!$request->has('code')) {
            return redirect()->route('googlesheet')->with('error', 'Invalid Google authentication response.');
        }

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setRedirectUri(config('app.url') . 'admin/google/sheets/callback');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        try {
            $tokenData = $client->fetchAccessTokenWithAuthCode($request->code);
            if (isset($tokenData['error'])) {
                return redirect()->route('googlesheet')->with('error', 'Google authentication failed.');
            }

            $accessToken  = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('googlesheet')->with('error', 'Google authentication failed.');
            }

            // Save token to DB (ensure same column names as Trait uses)
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
            if (session()->has('pending_sheet')) {
                $pending = session()->pull('pending_sheet');
                $sheetData = $this->createGoogleSheet($pending['title'], '', $pending['folderId']);

                if ($sheetData['status'] !== true) {
                    return redirect()->route('googlesheet')->with('error', $sheetData['message']);
                }

                // Save to Tbl_googleapikey
                $user = new Tbl_googleapikey();
                $user->staffid     = Auth::id();
                $user->driveid     = $sheetData['sheet_url'];
                $user->title       = $pending['title'];
                $user->type        = 'doc';
                $user->created_at  = time();
                $user->updated_at  = time();
                $user->description = '';
                $user->tenderid    = $pending['tenderid'] ?? null;
                $user->save();

                return redirect()->route('googlesheet')->with('success', 'Costing sheet created successfully.');
            }

            return redirect()->route('googlesheet')->with('success', 'Google Sheets connected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('googlesheet')->with('error', 'Something went wrong during Google authentication.');
        }
    }

    public function submitSheet(Request $request)
    {
        Log::info('submitSheet: Starting with request: ' . json_encode($request->all()));
        $sheet = Tbl_googleapikey::findOrFail($request->id);
        if (!$sheet) {
            return redirect()->back()->with('error', 'Sheet not found.');
        }

        $updatedsheet = $sheet->update([
            'final_price' => $request->final_price,
            'receipt' => $request->receipt,
            'budget' => $request->budget,
            'gross_margin' => $request->gross_margin,
            'remarks' => $request->remarks,
        ]);
        Log::info('submitSheet: Sheet submitted successfully.' . json_encode($updatedsheet));

        $tender = TenderInfo::findOrFail($sheet->tenderid);
        Log::info('Tender:' . json_encode($tender));
        if ($tender) {
            $this->timerService->stopTimer($tender, 'costing_sheet');

            // countdown to 48 hours before the tender due date and time
            $dueDate = new Carbon("{$tender->due_date} {$tender->due_time}");
            $cutoffDate = (clone $dueDate)->subHours(48); // Timer hits zero here
            $now = Carbon::now();

            // This gives positive or negative hours naturally
            $hrs = $now->diffInHours($cutoffDate, false);

            Log::info('Due Date: ' . $dueDate->toDateTimeString() .
                ' | Cutoff Date: ' . $cutoffDate->toDateTimeString() .
                ' | Current Time: ' . $now->toDateTimeString() .
                ' | Hours until/since cutoff: ' . $hrs);

            $this->timerService->startTimer($tender, 'costing_sheet_approval', $hrs);

            // update tender status to 6
            $tender->update(['status' => 6]);
        }

        if ($this->sendApproval($request->id)) {
            return redirect()->back()->with('success', 'Costing sheet submitted successfully. Approval request sent to team leader.');
        } else {
            return redirect()->back()->with('error', 'Costing sheet submitted but failed to send approval request. Please notify your team leader manually.');
        }
    }

    public function sendApproval($id)
    {
        $sheet = Tbl_googleapikey::findOrFail($id);
        $tender = TenderInfo::findOrFail($sheet->tenderid);
        $sender = null;
        if ($sheet->staffid) {
            $sender = User::findOrFail($sheet->staffid);
        } else {
            $sender = Auth::user();
        }

        $tl = User::where('role', 'team-leader')->where('team', $sender->team)->first();
        $admin = User::where('role', 'admin')->where('team', $sender->team)->first();

        $data = [
            'tlName' => $tl->name,
            'tender_name' => $sheet->title,
            'costingSheetLink' => $sheet->driveid,
            'tenderValue' => format_inr($tender->gst_values),
            'finalPrice' => format_inr($sheet->final_price),
            'receipt' => format_inr($sheet->receipt),
            'budget' => format_inr($sheet->budget),
            'grossMargin' => $sheet->gross_margin,
            'remarks' => $sheet->remarks,
            'dueDate' => date('d-m-Y', strtotime($tender->due_date)),
            'dueTime' => date('h:i A', strtotime($tender->due_time)),
            'teName' => $sender->name,
        ];
        Log::info('sendApproval: data: ' . json_encode($data));

        MailHelper::configureMailer($sender->email, $sender->app_password, $sender->name);
        $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';

        $mail = Mail::mailer($mailer)
            ->to($tl->email)
            ->cc([$admin->email])
            ->send(new CostingApprovalRequestMail($data));

        if ($mail) {
            Log::info('sendApproval: Email sent successfully');
            return true;
        } else {
            Log::error('sendApproval: Failed to send email');
            return false;
        }
    }
}
