<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('crm:test-gateways {email?}', function ($email = 'test@example.com') {
    $this->info("Testing Mailtrap SMTP connection (sending to {$email})...");
    try {
        Mail::raw('Test email from Laravel CRM Gateway Test.', function ($message) use ($email) {
            $message->to($email)
                    ->subject('Gateway Test Email');
        });
        $this->info('✅ Mailtrap Connection: SUCCESS');
    } catch (\Exception $e) {
        $this->error('❌ Mailtrap Connection: FAILED - ' . $e->getMessage());
    }

    $this->info('Testing Stripe SDK Integration...');
    try {
        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            throw new \Exception('STRIPE_SECRET is empty/not set in environment.');
        }
        \Stripe\Stripe::setApiKey($stripeSecret);
        \Stripe\Balance::retrieve();
        $this->info('✅ Stripe SDK Key: VALID');
    } catch (\Exception $e) {
        $this->error('❌ Stripe SDK Key: INVALID - ' . $e->getMessage());
    }
})->purpose('Test Mailtrap SMTP connection and Stripe SDK integration');
