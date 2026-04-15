<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Models\User;

use App\Services\AgentService;

class AgentTest extends TestCase
{
    use RefreshDatabase;
    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->postJson('/api/agent', [
            'message' => 'What posts do I have?'
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_error_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/agent', [
            // 'message' is missing
        ]);

        $response->assertStatus(422);
    }

    /*     public function test_valid_request_returns_jobid_and_conversationid(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/agent', [
            'message' => 'What posts do I have?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jobId', 'conversationId']);
    } */

    public function test_valid_request_returns_jobid_and_conversationid(): void
    {
        $user = User::factory()->create();

        // Mock AgentService — never calls Gemini
        $this->mock(AgentService::class, function ($mock) {
            $mock->shouldReceive('run')
                ->once()
                ->andReturn('Mocked response — you have 0 posts.');
        });

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/agent', [
            'message' => 'What posts do I have?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['jobId', 'conversationId']);
    }

    public function test_invalidjobId_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/agent/status/invalid-job-id');

        $response->assertStatus(404);
    }

    public function test_status_endpoint_returns_correct_structure(): void
    {
        $user = User::factory()->create();
        $jobId = (string) \Illuminate\Support\Str::uuid();

        \App\Models\AgentResult::create([
            'user_id' => $user->id,
            'job_id'  => $jobId,
            'status'  => 'completed',
            'message' => 'test message',
            'result'  => 'test result',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/agent/status/{$jobId}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'message', 'result']);
    }
}
