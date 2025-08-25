<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('Este es un email de prueba desde IGAC', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email IGAC');
            });
            
            $this->info('Email enviado a: ' . $email);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}