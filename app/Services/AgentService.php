<?php

namespace App\Services;

use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use App\Agent\Tools\GetPostStatus;
use App\Agent\Tools\GetAllPosts;
use Illuminate\Support\Facades\Cache;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;


class AgentService
{

    public function run(string $userMessage, int $userId, string $conversationId): string
    {
        $cacheKey = "agent_history_{$userId}_{$conversationId}"; // for multi conversation support

        // Load existing history from cache
        $history = Cache::get($cacheKey, []);

        // Add current user message to history
        $history[] = new UserMessage($userMessage);

        $tools = [
            (new GetPostStatus)->handle(),
            (new GetAllPosts)->handle(),
        ];
        /*
        What posts do I have?
        Which ones are in draft status?
        */

        //gemini-3-flash-preview
        //gemini-2.5-flash-lite
        //gemini-2.5-flash

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash-lite')
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
