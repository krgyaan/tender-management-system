<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Oauth2;
use App\Models\Tbl_google_access_token; // adjust namespace
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Token guard (auto-refresh per user):
 * Loads credentials.json.
 * Loads tokens from Tbl_google_access_token.
 * Refreshes when <120s to expiry, and persists the new tokens.
 */
class GmailTokenGuard
{
    public function clientForUser(int $userId): GoogleClient
    {
        $row = Tbl_google_access_token::where('userid', $userId)->first();
        if (!$row || !$row->access_token) throw new RuntimeException('NEEDS_CONNECT');

        $client = new GoogleClient();
        $client->setApplicationName('TMS - VolksEnergie Tender Management System');
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri(route('google.oauth.callback'));
        $client->setScopes([
            \Google\Service\Sheets::SPREADSHEETS,
            \Google\Service\Drive::DRIVE,
            \Google\Service\Drive::DRIVE_FILE,
            \Google\Service\Drive::DRIVE_METADATA,
            'email',
            'profile',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.modify',
        ]);

        $token = json_decode($row->access_token, true) ?: [];
        $client->setAccessToken($token);

        $expiresAt = $row->expires_at ? \Carbon\Carbon::parse($row->expires_at)->timestamp : ($token['created'] ?? time()) + ($token['expires_in'] ?? 0);
        if ($expiresAt - time() < 120) {
            if (!$row->refresh_token) throw new RuntimeException('RECONNECT_REQUIRED');
            $new = $client->fetchAccessTokenWithRefreshToken($row->refresh_token);
            if (isset($new['error'])) throw new RuntimeException('RECONNECT_REQUIRED');

            // Persist
            $row->access_token = json_encode($new);
            $row->expires_in   = $new['expires_in'] ?? null;
            if (isset($new['refresh_token'])) {
                $row->refresh_token = $new['refresh_token'];
            }
            $row->expires_at = Carbon::now()->addSeconds($new['expires_in'] ?? 0);
            $row->save();

            $client->setAccessToken($new);
        }

        return $client;
    }
}
