<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Tbl_google_access_token;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/')->with('success', 'You are already logged in!');
        }

        $name = request()->cookie('user_name');
        return view('auth.login', compact('name'));
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            $user = User::where('email', $request->email)->where('status', 1)->first();

            if ($user === null) {
                throw new \Exception('The provided credentials do not match our records.');
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new \Exception('The provided credentials do not match our records.');
            }

            Cookie::queue('user_name', $user->name, 60 * 24 * 7);
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        request()->session()->invalidate();
        Auth::logout();
        return redirect('/login');
    }

    public function storeGoogleOAuthToken(array $tokenData): bool
    {
        try {
            $accessToken = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['expires_in'] ?? null;
            $tokenType = $tokenData['token_type'] ?? null;
            $scope = $tokenData['scope'] ?? null;

            if (!$accessToken) {
                return false;
            }

            Tbl_google_access_token::updateOrCreate(
                ['userid' => auth()->id()],
                [
                    'access_token'  => json_encode($tokenData),
                    'refresh_token' => $refreshToken,
                    'expires_in'    => $expiresIn,
                    'token_type'    => $tokenType,
                    'scope'         => $scope,
                    'ip'            => request()->ip(),
                    'updated_at'    => now(),
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store Google OAuth token: ' . $e->getMessage());
            return false;
        }
    }

    public function createGoogleOAuthPlaceholder()
    {
        try {
            Tbl_google_access_token::updateOrCreate(
                ['userid' => Auth::id()],
                [
                    'access_token'  => null,
                    'refresh_token' => null,
                    'expires_in'    => null,
                    'token_type'    => null,
                    'scope'         => null,
                    'updated_at'    => now(),
                    'ip'            => request()->ip(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to create Google OAuth placeholder: ' . $e->getMessage());
        }
    }

    /**
     * Initiate Google OAuth flow with extended scopes for Gmail and offline access.
     * Redirects user to Google consent screen.
     */
    public function connectGoogle()
    {
        $client = new \Google\Client();
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
        // Create placeholder record
        $this->createGoogleOAuthPlaceholder();
        // Redirect to Google OAuth consent screen
        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Callback endpoint to handle Google OAuth response and store tokens.
     */
    public function googleOAuthCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('dashboard')->with('error', 'Google authentication failed: ' . $request->error);
        }
        if (!$request->has('code')) {
            return redirect()->route('dashboard')->with('error', 'Invalid Google authentication response.');
        }
        try {
            $client = new \Google\Client();
            $client->setApplicationName('TMS - VolksEnergie Tender Management System');
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->setRedirectUri(route('google.oauth.callback'));
            $client->setAccessType('offline');
            $client->setPrompt('consent');
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
            $tokenData = $client->fetchAccessTokenWithAuthCode($request->code);
            if (isset($tokenData['error'])) {
                return redirect()->route('dashboard')->with('error', 'Google authentication failed: ' . $tokenData['error']);
            }
            $this->storeGoogleOAuthToken($tokenData);
            return redirect()->route('dashboard')->with('success', 'Google account connected successfully!');
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
