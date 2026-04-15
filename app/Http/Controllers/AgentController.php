<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentResult;
use App\Jobs\AgentJob;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function run(Request $request) {
        $request->validate([
            'message' => 'required|string|max:500',
            'conversationId' => 'sometimes|string|uuid'
        ]);

        if(!$request->input('conversationId')) {
            $conversationId = Str::uuid();
        } else {
            $conversationId = $request->input('conversationId');
        }

        $jobId = (string) Str::uuid();

        $validated['job_id']       = $jobId;
        $validated['status']       = 'pending';
        $validated['message']  = $request->input('message');
        $validated['user_id'] = auth()->id();

        AgentResult::create($validated);
        
        AgentJob::dispatch($request->input('message'), $jobId, auth()->id(), $conversationId);

        return response()->json(['jobId' => $jobId, 'conversationId' => $conversationId ]);
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

    public function logout(Request $request) {        
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }   
}
