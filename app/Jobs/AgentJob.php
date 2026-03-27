<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\AgentResult;
use App\Services\AgentService;
use Illuminate\Support\Facades\Auth;

class AgentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $message, public string $jobId, public int $userId) {}

    /**
     * Execute the job.
     */
    public function handle(AgentService $agentService): void
    {
        $record = AgentResult::where('job_id', $this->jobId)->first();

        $record->status = 'processing';
        $record->save();

        Auth::loginUsingId($this->userId);

        $result = $agentService->run($this->message);

        $record->status = 'completed';
        $record->result = $result; // missing this line

        $record->save();
    }

    public function failed(\Throwable $exception): void
    {
        AgentResult::where('job_id', $this->jobId)
            ->update([
                'status' => 'failed',
                'result' => $exception->getMessage()
            ]);
    }
}
