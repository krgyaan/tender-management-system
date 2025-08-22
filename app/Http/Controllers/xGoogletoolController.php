<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google\Client;
use App\Models\User;
use Google\Service\Docs;
use Google\Service\Drive;
use App\Models\TenderInfo;
use Google\Service\Sheets;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Services\TimerService;
use App\Models\Tbl_googleapikey;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Google\Service\Sheets\Spreadsheet;
use Illuminate\Support\Facades\Config;
use App\Models\Tbl_google_access_token;
use App\Mail\CostingApprovalRequestMail;
use Google\Service\Drive as Google_Service_Drive;

class GoogletoolController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }
    
    public $reason = [
        9 => 'OEM Bidders only',
        10 => 'Not allowed by OEM',
        11 => 'Not Eligible',
        12 => 'Product type bid',
        13 => 'Small Value Tender',
        14 => 'Product not available',
        15 => 'An electrical Contractor license needed',
    ];

    public $commercial = [
        1 => 'Item Wise GST Inclusive',
        2 => 'Item Wise Pre GST',
        3 => 'Overall GST Inclusive',
        4 => 'Overall Pre GST',
    ];

    public $maf = [
        1 => 'Yes (project specific)',
        2 => 'Yes (general)',
        3 => 'No',
    ];

    public $tenderFees = [
        1 => 'Pay on Portal',
        2 => 'NEFT/RTGS',
        3 => 'DD',
        4 => 'Not Applicable',
    ];

    public $emdReq = [
        1 => 'Yes',
        2 => 'No',
        3 => 'Exempt',
    ];

    public $emdOpt = [
        1 => 'Pay on Portal',
        2 => 'NEFT/RTGS',
        3 => 'DD',
        4 => 'BG',
        5 => 'Not Applicable',
    ];

    public $revAuction = [
        1 => 'Yes',
        2 => 'No',
    ];

    public $teams = [
        'AC' => 'AC',
        'DC' => 'DC',
    ];
    
    private $client; // Declare the $client property
    private $service; // Declare the $service property
    private $docsService; // Declare the $docsService property
    private $sheetsService; // Declare the $sheetsService property
    private $files; // Declare the $files property
    
    public function _create_client($type = '')
    {
        $credentialsdata = DB::table('tbl_googleapis')->where('id', '1')->first();
        Log::info('_create_client: Starting with type=' . $type);
        $credentials = array(
            'web' => array(
                'client_id' => $credentialsdata->client_id,
                'project_id' => $credentialsdata->project_id,
                'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri' => 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                'client_secret' => $credentialsdata->client_secret,
                'redirect_uris' => array($credentialsdata->redirect_url),
            ),
        );

        $this->client = new Client();
        $this->client->setApplicationName('TMS - VolksEnergie Tender Management System');
        Log::info('_create_client: setApplicationName called');
        if ($type == 'readonly') {
            $this->client->setScopes([
                Docs::DOCUMENTS_READONLY,
                Sheets::SPREADSHEETS_READONLY,
                Drive::DRIVE_READONLY,
                Drive::DRIVE_METADATA_READONLY,
                'email',
                'profile',
            ]);
        } else {
            $this->client->setScopes([
                Docs::DOCUMENTS,
                Sheets::SPREADSHEETS,
                Drive::DRIVE,
                Drive::DRIVE_FILE,
                Drive::DRIVE_METADATA,
                'email',
                'profile',
            ]);
        }

        $this->client->setAccessType('offline');
        $this->client->setAuthConfig($credentials);
        Log::info('_create_client: setAuthConfig called');
    }

    public function _set_service()
    {
        Log::info('_set_service: Starting');
        $userdata = Auth::user();
        $account = tbl_google_access_token::where('userid', $userdata->id);

        if ($account->count() > 0) {
            $acoundata = $account->first();
            $accessToken = json_decode($acoundata->access_token, true);
            Log::info('_set_service: access token is ' . json_encode($accessToken));
            $_SESSION['access_token'] = $accessToken;
            $this->client->setAccessToken($accessToken);
        }

        if (!isset($_SESSION['access_token'])) {
            Log::info('_set_service: No access token is set');
            $auth_url = $this->client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }

        if ($this->client->isAccessTokenExpired()) {
            Log::info('_set_service: Access token is expired');
            if ($this->client->getRefreshToken()) {
                Log::info('_set_service: Refresh token is present');
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $_SESSION['access_token'] = $this->client->getAccessToken();
            } else {
                Log::info('_set_service: Refresh token is not present');
                $authUrl = $this->client->createAuthUrl();
                Log::info('_set_service: Redirecting to ' . $authUrl);
                echo '<script> window.location = "' . $authUrl . '"; </script>';
            }
        }
        // Get a new instance of the Google Drive service
        $this->service = new Drive($this->client);
        $this->docsService = new Docs($this->client);
        $this->sheetsService = new Sheets($this->client);
        $this->files = new DriveFile($this->client);
        Log::info('_set_service: Done');
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

        $query->orderByDesc('due_date');

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

    public function integrate()
    {
        Log::info('integrate: Starting');
        $this->_create_client();
        $authUrl = $this->client->createAuthUrl();
        Log::info('integrate: authUrl is ' . $authUrl);
        return redirect($authUrl);
    }

    public function googletoolsredirects(Request $request)
    {
        Log::info('googletoolsredirects: Starting with request: ' . json_encode($request->all()));
        $code = $request->code;
        if ($code) {
            Log::info('googletoolsredirects: code is ' . $code);

            $this->_create_client();
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);
            $userdata = Auth::user();
            $usercount = tbl_google_access_token::where('userid', $userdata->id)->count();
            $tokeandata = json_encode($this->client->getAccessToken());

            if ($usercount > 0) {
                Log::info('googletoolsredirects: Updating user ' . $userdata->id);
                $user = tbl_google_access_token::where('userid', $userdata->id)->first();
                $user->access_token = "$tokeandata";
                $user->updated_at = strtotime(date('Y-m-d H:i:s'));
                $user->ip = $_SERVER['REMOTE_ADDR'];
                $user->save();
            } else {
                Log::info('googletoolsredirects: Creating new user ' . $userdata->id);
                $user = new Tbl_google_access_token();
                $user->access_token = "$tokeandata";
                $user->userid = $userdata->id;
                $user->status = '1';
                $user->created_at = strtotime(date('Y-m-d H:i:s'));
                $user->updated_at = strtotime(date('Y-m-d H:i:s'));
                $user->ip = $_SERVER['REMOTE_ADDR'];
                $user->save();
            }


            return redirect()->route('googlesheet')->with('success', 'Battery prices added successfully.');
        } else {
            Log::info('googletoolsredirects: No code provided');
        }
    }


    public function googletoolssave(Request $request)
    {
        Log::info('googletoolssave: Integrating with sheet for : ' . Auth::user()->name);
        $this->integrate();
        Log::info('googletoolssave: Starting with request: ' . json_encode($request->all()));
        $this->_create_client();
        $title = $request->title;
        if ($title) {
            Log::info('googletoolssave: _set_service called');
            $this->_set_service();

            try {
                $driveService = new Google_Service_Drive($this->client);
                Log::info('googletoolssave: created driveService');
                // $folderMetadata = new \Google_Service_Drive_DriveFile([
                //     'name' => 'TMS-PricingSheet',
                //     'mimeType' => 'application/vnd.google-apps.folder'
                // ]);

                // $folder = $driveService->files->create($folderMetadata, ['fields' => 'id']);
                // $folderId = $folder->id;


                Log::info('googletoolssave: creating spreadsheet');
                $spreadsheet = new Spreadsheet([
                    'properties' => [
                        'title' => $title
                    ]
                ]);

                Log::info('googletoolssave: creating spreadsheet');
                $spreadsheet = $this->sheetsService->spreadsheets->create($spreadsheet, [
                    'fields' => 'spreadsheetId'
                ]);

                Log::info('googletoolssave: created spreadsheet');
                $spreadsheetId = $spreadsheet->spreadsheetId;

                Log::info('googletoolssave: getting file');
                $file = $driveService->files->get($spreadsheetId, ['fields' => 'parents']);
                Log::info('googletoolssave: got file');
                $previousParents = join(',', $file->parents);

                Log::info('googletoolssave: updating file');
                
                $parentFolder = match (Auth::user()->team) {
                    'DC' => '1GTKETwOnO29y-XbxCMPjhPmsjs7rRS_I',
                    'AC' => '1o_8fZssZ9aVO0HNLQTuQKIrsciGpHQ3g',
                };
                Log::info("Parent Drive for Auth::user()->team : $parentFolder");
                
                $driveService->files->update($spreadsheetId, new DriveFile(), [
                    'addParents' => $parentFolder,
                    'removeParents' => $previousParents,
                    'fields' => 'id, parents'
                ]);

                Log::info('googletoolssave: updated file');
                //Get sheet link
                Log::info('googletoolssave: getting sheet link');
                $sheetLink = $this->sheetsService->spreadsheets->get($spreadsheetId, [
                    'fields' => 'spreadsheetUrl'
                ]);
                Log::info('googletoolssave: got sheet link');
                $spreadsheetId = $sheetLink->spreadsheetUrl;
                $userdata = Auth::user();
                Log::info('googletoolssave: got user data');
                $user = new Tbl_googleapikey();
                $user->staffid = $userdata->id;
                $user->driveid = $spreadsheetId;
                $user->title = $title;
                $user->type = 'doc';
                $user->created_at = strtotime(date('Y-m-d H:i:s'));
                $user->updated_at = strtotime(date('Y-m-d H:i:s'));
                $user->description = '';
                $user->tenderid = $request->TenderInfo;
                Log::info('googletoolssave: saving user data');
                $user->save();

                Log::info('googletoolssave: saved user data');

                return redirect()->route('googlesheet')->with('success', 'Sheet created successfully.');
            } catch (\Exception $e) {
                Log::error('googletoolssave: error: ' . $e->getMessage());
                return redirect()->route('googlesheet')->with('error', 'Failed to create sheet: ' . $e->getMessage());
            }
        }
    }

    public function googletoolview($id)
    {
        $this->_create_client();
        $this->_set_service();
        $sheet = Tbl_googleapikey::findOrFail($id);
        $data['tender'] = TenderInfo::findOrFail($sheet->tenderid);
        $data['rfq'] = Rfq::where('tender_id', $sheet->tenderid)->first();
        return view('googletool.view', $data);
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
            'budget' => $request->budget,
            'receipt' => $request->receipt,
            'gross_margin' => $request->gross_margin,
            'remarks' => $request->remarks,
        ]);
        Log::info('submitSheet: Sheet submitted successfully.' . json_encode($sheet->tenderid));
        
        $tender = TenderInfo::findOrFail($sheet->tenderid);
        Log::info('Tender:' . json_encode($tender));
        if($tender) {
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
            return redirect()->back()->with('success', 'Sheet submitted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to submit sheet.');
        }
        return redirect()->back()->with('error', 'Failed to submit sheet.');
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
