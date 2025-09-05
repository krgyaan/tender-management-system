<?php

namespace App\Traits;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Google\Service\Drive\DriveFile;
use Google\Service\Sheets\Spreadsheet;
use App\Models\Tbl_google_access_token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

trait HandlesGoogleSheet
{
    private $googleClient;
    private $sheetsService;
    private $driveService;

    public function setGoogleRedirectUri(string $uri)
    {
        $this->googleRedirectUri = $uri;
        Log::info("OAuth Redirect URI from Controller: $uri");
    }

    public function createGoogleSheetOld(string $title, string $type = '', ?string $folderId = null): ?array
    {
        try {
            Log::info("Starting creation for title: $title");

            if (!$this->initializeGoogleClient($type)) {
                return null;
            }

            $setupResult = $this->setupGoogleServices();
            Log::info("setupGoogleServices: ", ["response" => json_encode($setupResult)]);

            if (is_array($setupResult) && $setupResult['action'] === 'connect_google') {
                return [
                    'status' => 'redirect',
                    'auth_url' => $setupResult['auth_url']
                ];
            }

            if ($setupResult !== true) {
                return back()->with('error', 'Failed to initialize Google Services.');
            }

            // Create spreadsheet
            $spreadsheet = new Spreadsheet(['properties' => ['title' => $title]]);
            $createdSpreadsheet = $this->sheetsService->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId,spreadsheetUrl'
            ]);

            $spreadsheetId = $createdSpreadsheet->spreadsheetId;
            Log::info("Created spreadsheet with ID: $spreadsheetId");

            // Move to folder
            $this->moveToTeamFolder($spreadsheetId, $folderId);

            // Return final info
            return [
                'sheet_id' => $spreadsheetId,
                'sheet_url' => $createdSpreadsheet->spreadsheetUrl
            ];
        } catch (Exception $e) {
            Log::error("Exception occurred: " . $e->getMessage());
            if (app()->environment('local')) {
                throw $e;
            }
            return null;
        }
    }

    public function createGoogleSheet(string $title, string $type = '', ?string $folderId = null): ?array
    {
        try {
            Log::info("Starting creation for title: $title");

            if (!$this->initializeGoogleClient($type)) {
                return ['status' => false, 'message' => 'Google client initialization failed.'];
            }

            $setupResult = $this->setupGoogleServices();

            // If OAuth required → pass redirect up the chain
            if ($setupResult['status'] === 'redirect') {
                return $setupResult;
            }

            if ($setupResult['status'] !== true) {
                return ['status' => false, 'message' => 'Failed to initialize Google services.'];
            }

            // Create spreadsheet
            $spreadsheet = new Spreadsheet(['properties' => ['title' => $title]]);
            $createdSpreadsheet = $this->sheetsService->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId,spreadsheetUrl'
            ]);

            $spreadsheetId = $createdSpreadsheet->spreadsheetId;
            Log::info("Created spreadsheet with ID: $spreadsheetId");

            // Move to folder
            $this->moveToTeamFolder($spreadsheetId, $folderId);

            return [
                'status' => true,
                'sheet_id' => $spreadsheetId,
                'sheet_url' => $createdSpreadsheet->spreadsheetUrl
            ];
        } catch (Exception $e) {
            Log::error("Exception occurred: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Initialize Google Client
     */
    private function initializeGoogleClient(string $type = ''): bool
    {
        try {
            $this->googleClient = new Client();
            $this->googleClient->setApplicationName('TMS - VolksEnergie Tender Management System');

            // Dynamic scopes
            $scopes = [
                Sheets::SPREADSHEETS,
                Drive::DRIVE,
                Drive::DRIVE_FILE,
                Drive::DRIVE_METADATA,
                'email',
                'profile',
            ];

            if ($type === 'readonly') {
                $scopes = [
                    Sheets::SPREADSHEETS_READONLY,
                    Drive::DRIVE_READONLY,
                    Drive::DRIVE_METADATA_READONLY,
                    'email',
                    'profile',
                ];
            }

            $this->googleClient->setScopes($scopes);
            $this->googleClient->setAccessType('offline');
            $this->googleClient->setPrompt('consent');
            $redirectUri = $this->googleRedirectUri ?? (config('app.url') . 'admin/google/sheets/callback');
            $this->googleClient->setRedirectUri($redirectUri);
            $this->googleClient->setAuthConfig(storage_path('app/google/credentials.json'));

            Log::info('Google Client initialized successfully');
            return true;
        } catch (Exception $e) {
            Log::error('Failed to initialize - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Setup Google Services and handle OAuth if needed.
     */
    private function setupGoogleServices()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('No authenticated user found');
                return ['status' => false, 'message' => 'No authenticated user found.'];
            }

            $integrationCheck = $this->checkGoogleIntegration($user->id);

            // If integration not ready → return OAuth redirect info
            if (!$integrationCheck['status']) {
                if ($integrationCheck['action'] === 'connect_google') {
                    // Ensure placeholder token record exists
                    Tbl_google_access_token::updateOrCreate(
                        ['userid' => $user->id],
                        [
                            'access_token'  => null,
                            'refresh_token' => null,
                            'expires_in'    => null,
                            'token_type'    => null,
                            'scope'         => null,
                            'updated_at'    => now(),
                        ]
                    );

                    Log::info("OAuth required for user {$user->id}");
                    Log::info("integrationCheck: " . json_encode($integrationCheck));
                    return [
                        'status' => 'redirect',
                        'auth_url' => $integrationCheck['auth_url'],
                        'message' => $integrationCheck['message']
                    ];
                }

                return ['status' => false, 'message' => $integrationCheck['message']];
            }

            // Valid token found → refresh if needed
            $this->validateAndRefreshToken($integrationCheck['record']);

            // Initialize services
            $this->sheetsService = new Sheets($this->googleClient);
            $this->driveService  = new Drive($this->googleClient);

            Log::info('Google Services setup completed');
            return ['status' => true];
        } catch (Exception $e) {
            Log::error('Failed to setup Google services - ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Centralized token validation & refresh
     */
    private function validateAndRefreshToken($tokenRecord): void
    {
        $accessToken = json_decode($tokenRecord->access_token, true);
        $this->googleClient->setAccessToken($accessToken);

        if ($this->googleClient->isAccessTokenExpired()) {
            Log::info('Access token expired, attempting refresh');

            $refreshToken = $tokenRecord->refresh_token ?? null;
            if (!$refreshToken) {
                throw new Exception('No refresh token available. Please re-integrate.');
            }

            $newToken = $this->googleClient->fetchAccessTokenWithRefreshToken($refreshToken);
            if (isset($newToken['error'])) {
                throw new Exception('Failed to refresh token: ' . $newToken['error']);
            }

            $tokenRecord->update([
                'access_token'  => json_encode($this->googleClient->getAccessToken()),
                'refresh_token' => $refreshToken,
                'updated_at'    => now(),
                'ip'            => request()->ip(),
            ]);

            Log::info('Access token refreshed successfully');
        }
    }

    /**
     * Move spreadsheet to team or custom folder
     */
    private function moveToTeamFolder(string $spreadsheetId, ?string $customFolderId = null): bool
    {
        try {
            $user = Auth::user();
            $parentFolder = $customFolderId;

            if (!$parentFolder) {
                $teamFolders = [
                    'DC' => '1GTKETwOnO29y-XbxCMPjhPmsjs7rRS_I',
                    'AC' => '1o_8fZssZ9aVO0HNLQTuQKIrsciGpHQ3g',
                ];

                $parentFolder = $teamFolders[$user->team] ?? 'DEFAULT_FOLDER_ID';
                if ($parentFolder === 'DEFAULT_FOLDER_ID') {
                    Log::warning("No team mapping for {$user->team}, using default folder");
                }
            }

            $file = $this->driveService->files->get($spreadsheetId, ['fields' => 'parents']);
            $previousParents = join(',', $file->parents);

            $this->driveService->files->update($spreadsheetId, new DriveFile(), [
                'addParents' => $parentFolder,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'
            ]);

            Log::info("Moved sheet to folder: $parentFolder");
            return true;
        } catch (Exception $e) {
            Log::error('Failed to move sheet - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure user has a valid Google connection or auto-initiate OAuth
     */
    private function checkGoogleIntegration(?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            Log::error('Google integration check failed - no authenticated user');
            return ['status' => false, 'action' => 'error', 'message' => 'No authenticated user found.'];
        }

        $tokenRecord = Tbl_google_access_token::where('userid', $userId)->first();

        if (!$tokenRecord || empty($tokenRecord->refresh_token)) {
            Log::warning("No valid Google integration for user {$userId}. Starting OAuth connection.");

            $oauthClient = new Client();
            $oauthClient->setApplicationName('TMS - VolksEnergie Tender Management System');
            $oauthClient->setAuthConfig(storage_path('app/google/credentials.json'));

            $oauthClient->setScopes([
                Sheets::SPREADSHEETS,
                Drive::DRIVE,
                Drive::DRIVE_FILE,
                Drive::DRIVE_METADATA,
                'email',
                'profile',
            ]);

            $oauthClient->setAccessType('offline');
            $oauthClient->setPrompt('consent');

            // Add the redirect URI here too!
            $redirectUri = $this->googleRedirectUri ?? (config('app.url') . 'admin/google/sheets/callback');
            $oauthClient->setRedirectUri($redirectUri);

            // Debug logging
            Log::info('Trait OAuth Redirect URI set to: ' . $redirectUri);

            $authUrl = $oauthClient->createAuthUrl();
            Log::info('Trait Generated auth URL: ' . $authUrl);

            return [
                'status' => false,
                'action' => 'connect_google',
                'auth_url' => $authUrl,
                'message' => 'Redirecting to Google account connection...'
            ];
        }

        return ['status' => true, 'message' => 'Google integration found.', 'record' => $tokenRecord];
    }
}
