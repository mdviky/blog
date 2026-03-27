<?php

namespace App\Services;

use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use App\Agent\Tools\GetPostStatus;
use App\Agent\Tools\GetAllPosts;

class AgentService
{
    public function run(string $userMessage): string
    {
        $tools = [
            (new GetPostStatus)->handle(),
            (new GetAllPosts)->handle(),
        ];

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-3-flash-preview')
            ->withMaxSteps(5)
            ->withPrompt($userMessage)
            ->withTools($tools)
            ->asText();

        return $response->text;
    }
}