<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApplicationException;
use App\Http\Controllers\Api\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use \GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GptController extends BaseController
{
    public function complete(Request $request)
    {                
        $models = [
            'llama-3-sonar-small-32k-chat',
            'llama-3-sonar-small-32k-online',
            'llama-3-sonar-large-32k-chat',
            'llama-3-sonar-large-32k-online',
            'llama-3-8b-instruct',
            'llama-3-70b-instruct',
            'mixtral-8x7b-instruct'
        ];
        
        $defaultModel = 'llama-3-sonar-small-32k-online';

        $request->validate([
            'model' => 'nullable|in:' . join(',', $models),
            'prompt' => 'required|string'
        ]);

        $model = $request->get('model', $defaultModel);
        $prompt = $request->get('prompt');
                
        $body = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ];

        $client = new Client();
        try {
            $response = $client->request('POST', 'https://api.perplexity.ai/chat/completions', [
                'body' => json_encode($body),
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('app.perplexity_token')
                ],
            ]);
        } catch (RequestException $e) {
            throw new ApplicationException('invalid_perplexity_request', 500, $e);
        }

        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);
        $content = $data['choices'][0]['message']['content'];

        return response()->json([
            'content' => $content, 
            'message' => 'Success.'
        ]);
    }
}
