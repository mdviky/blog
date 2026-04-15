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

        $tools = [
            (new GetPostStatus)->handle(),
            (new GetAllPosts)->handle(),
            (new CreatePost)->handle(),
            (new UpdatePostStatus)->handle(),
        ];
        /*
        What posts do I have?
        Which ones are in draft status?
        */

        //gemini-3-flash-preview
        //gemini-2.5-flash-lite
        //gemini-2.5-flash
        //gemini-2.5-pro
        //gemini-3.1-flash-lite-preview

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-3.1-flash-lite-preview')
            ->withSystemPrompt('You are a blog management assistant. You help users manage their blog posts only. You can get post status, list all posts, create new posts, and update post status. If a user asks about anything unrelated to their blog posts, politely decline and redirect them to ask about their posts.')
            //->using(Provider::OpenAI, 'gpt-5-nano')
            ->withMaxSteps(5)
            ->withMessages($history)
            ->withTools($tools)
            ->asText();

        // Append assistant response to history
        $history[] = new AssistantMessage($response->text);

        // Save updated history — expires in 2 hours
        Cache::put($cacheKey, $history, now()->addHours(2));

        return $response->text;
    }
}
