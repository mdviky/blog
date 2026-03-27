<?php

namespace App\Http\Controllers;

use App\Services\AgentService;
use Illuminate\Http\Request;
use App\Models\AgentResult;
use App\Jobs\AgentJob;
use Illuminate\Support\Str;


class AgentController extends Controller
{
    public function run(Request $request) {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $jobId = (string) Str::uuid();

        $validated['job_id']       = $jobId;
        $validated['status']       = 'pending';
        $validated['message']  = $request->input('message');
        $validated['user_id'] = auth()->id();

        AgentResult::create($validated);
        
        AgentJob::dispatch($request->input('message'), $jobId, auth()->id() );

        return response()->json(['jobId' => $jobId]);
    }

    public function status($jobId) {
        $record = AgentResult::where('job_id', $jobId)->first();

        if (!$record) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json([
            'status' => $record->status,
            'message' => $record->message,
            'result' => $record->result
        ]);
    }
}
