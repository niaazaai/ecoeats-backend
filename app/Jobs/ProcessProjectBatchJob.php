<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProjectBatchJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public Project $project
    ) {}

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        // Process the project
        Log::info('Processing project', [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
        ]);

        // Example: Update project status or perform some operation
        // $this->project->update(['processed_at' => now()]);
    }
}
