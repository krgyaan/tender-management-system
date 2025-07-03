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
use App\Models\Tbl_googleapikey;
use App\Models\Google_drive_model;
use App\Models\Googletools_module;
use Illuminate\Support\Facades\DB;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;
use App\Models\GoogletoolAjex_model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Google\Service\Sheets\Spreadsheet;
use Illuminate\Support\Facades\Config;
use App\Mail\CostingApprovalRequestMail;
use App\Models\Tbl_google_access_token;
use Google\Service\Drive as Google_Service_Drive;
use App\Services\TimerService;

class GoogletoolController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

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
        Log::info('googlesheet: Starting');
        $userdata = Auth::user();
        Log::info('googlesheet: User is ' . $userdata->id);
        $accountcount = DB::table('tbl_google_access_tokens')->where('userid', $userdata->id)->count();
        Log::info('googlesheet: Account count is ' . $accountcount);
        $data['drivedata'] = DB::table('tbl_googleapikeys')->get();
        Log::info('googlesheet: Got drivedata');

        $pendingSheets = TenderInfo::with('users', 'statuses', 'sheet')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->where(function ($query) {
                $query->whereHas('sheet', function ($q) {
                    $q->whereNull('final_price');
                })
                    ->orDoesntHave('sheet');
            })
            ->orderByDesc('due_date')
            ->get();

        $submittedSheets = TenderInfo::with('users', 'statuses', 'sheet')
            ->where('deleteStatus', '0')
            ->where('tlStatus', '1')
            ->whereHas('sheet', function ($query) {
                $query->where('final_price', '!=', null);
            })
            ->orderByDesc('due_date')
            ->get();

        $data['pendingSheets'] = $pendingSheets;
        $data['submittedSheets'] = $submittedSheets;
        Log::info('googlesheet: Got tender_infos');

        if ($accountcount > 0) {
            $data['accountstatus'] = '1';
            Log::info('googlesheet: Account status is 1');
        } else {
            $data['accountstatus'] = '0';
            Log::info('googlesheet: Account status is 0');
        }
        $data['title'] = "";
        Log::info('googlesheet: Done');
        return view('googletool.sheets', $data);
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
                $driveService->files->update($spreadsheetId, new DriveFile(), [
                    'addParents' => '1GTKETwOnO29y-XbxCMPjhPmsjs7rRS_I',
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
        Log::info('googletoolview: Starting with id=' . $id);
        $this->_create_client();
        Log::info('googletoolview: client created');
        $this->_set_service();
        Log::info('googletoolview: _set_service called');
        $data['driveid'] = $id;
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
            return redirect()->back()->with('success', 'Sheet submitted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to submit sheet.');
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
