<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $response = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 150,
        ]);

        return response()->json(['text' => $response['choices'][0]['text']]);
    }
}
