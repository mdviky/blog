<?php

namespace App\Services;

use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use App\Agent\Tools\GetPostStatus;
use App\Agent\Tools\GetAllPosts;
use App\Agent\Tools\UpdatePostStatus;
use Illuminate\Support\Facades\Cache;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Illuminate\Support\Facades\RateLimiter;

use Prism\Relay\Facades\Relay;


use App\Agent\Tools\CreatePost;


class AgentService
{

    public function run(string $userMessage, int $userId, string $conversationId): string
    {
        $rateLimitKey = "agent_api_calls_{$userId}";

        if (RateLimiter::tooManyAttempts($rateLimitKey, 50)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return "You have reached your daily limit. Try again in {$seconds} seconds.";
        }

        RateLimiter::hit($rateLimitKey, 86400); // 86400 seconds = 24 hours
        $cacheKey = "agent_history_{$userId}_{$conversationId}"; // for multi conversation support

        // Load existing history from cache
        $history = Cache::get($cacheKey, []);

        // Add current user message to history
        $history[] = new UserMessage($userMessage);

        $appTools = [
            (new GetPostStatus)->handle(),
            (new GetAllPosts)->handle(),
            (new CreatePost)->handle(),
            (new UpdatePostStatus)->handle(),
        ];
          // MCP filesystem tools via Relay (reads your project files)
        $mcpTools = Relay::tools('filesystem');

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-3.1-flash-lite-preview')
            ->withSystemPrompt(
                'You are a Laravel coding assistant. You have access to the project filesystem. ' .
                'When asked about models, read files in app/Models/. ' .
                'Check for $fillable or $guarded properties to verify mass-assignment protection.'
            )
            ->withTools([...$appTools, ...$mcpTools]) // ✅ merge both
            ->withMaxSteps(5) // needs more steps for filesystem reads
            ->withMessages($history)
            ->asText();

        $history[] = new AssistantMessage($response->text);
        Cache::put($cacheKey, $history, now()->addHours(2));

        return $response->text;
    }
}
