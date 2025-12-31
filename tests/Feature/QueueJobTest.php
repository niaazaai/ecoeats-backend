<?php

use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('welcome email job can be dispatched', function () {
    Queue::fake();

    $user = User::factory()->create();

    SendWelcomeEmailJob::dispatch($user);

    Queue::assertPushed(SendWelcomeEmailJob::class, function ($job) use ($user) {
        return $job->user->id === $user->id;
    });
});

test('welcome email job has correct configuration', function () {
    $user = User::factory()->create();
    $job = new SendWelcomeEmailJob($user);

    expect($job->tries)->toBe(3)
        ->and($job->backoff)->toBe(60)
        ->and($job->timeout)->toBe(30);
});

