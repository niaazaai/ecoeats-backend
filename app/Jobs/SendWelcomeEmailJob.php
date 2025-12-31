<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds
    public int $timeout = 30; // seconds

    public function __construct(
        public User $user
    ) {}

    public function handle(): void
    {
        try {
            // In a real app, you'd send an actual email here
            // Mail::to($this->user->email)->send(new WelcomeMail($this->user));
            
            Log::info('Welcome email sent', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendWelcomeEmailJob failed permanently', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
