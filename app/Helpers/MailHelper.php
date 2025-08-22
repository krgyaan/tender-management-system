<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    public static function configureMailer($username, $password, $name)
    {
        try {
            Log::info('Starting mailer configuration for username: ' . $username);

            Config::set('mail.mailers.dynamic', [
                'transport' => 'smtp',
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587), // Ensure the port is correct
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => $username,
                'password' => $password,
                'from' => [
                    'address' => $username,
                    'name' => $name . ' From ' . env('APP_NAME'),
                ]
            ]);

            $configuredMailer = Config::get('mail.mailers.dynamic');
            if (!$configuredMailer) {
                Log::error('Mail not configured for username: ' . $username);
            } else {
                Log::info('Mailer successfully configured with: ' . json_encode($configuredMailer));
            }
        } catch (\Exception $exception) {
            Log::error('Mailer configuration failed for username: ' . $username . ' with error: ' . $exception->getMessage());
            return false;
        }

        return true;
    }
}
