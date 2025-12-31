<?php

return [
    'domain' => null,
    'path' => 'horizon',
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', 'horizon'),
    'middleware' => ['web', \App\Http\Middleware\EnsureUserHasRole::class.':admin'],
    'waits' => [
        'redis:default' => 60,
    ],
    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],
    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],
    'fast_termination' => false,
    'balance' => env('HORIZON_BALANCE', 'auto'),
    'auto_scaling' => [
        'enabled' => false,
    ],
    'max_processes' => env('HORIZON_MAX_PROCESSES', 10),
    'min_processes' => 1,
    'balance_max_shift' => env('HORIZON_BALANCE_MAX_SHIFT', 1),
    'balance_cooldown' => env('HORIZON_BALANCE_COOLDOWN', 3),
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'max_processes' => 10,
                'min_processes' => 1,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
            ],
        ],
        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'max_processes' => 3,
                'tries' => 3,
                'timeout' => 60,
            ],
        ],
    ],
];

