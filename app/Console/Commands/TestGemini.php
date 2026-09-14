<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestGemini extends Command
{
    protected $signature = 'ai:test-gemini';

    protected $description = 'Test Gemini connectivity and structured JSON output';

    public function handle(): int
    {
        $endpoint = config('services.ai.endpoint');
        $apiKey = config('services.ai.key');
        $model = config('services.ai.model');

        $this->info('Testing Gemini...');
        $this->line('Endpoint: ' . $endpoint);
        $this->line('Model: ' . $model);
        $this->line('API key configured: ' . (filled($apiKey) ? 'YES' : 'NO'));

        if (blank($endpoint) || blank($apiKey) || blank($model)) {
            $this->error('AI configuration is incomplete.');
            return self::FAILURE;
        }

        $response = Http::timeout(60)
            ->withToken($apiKey)
            ->acceptJson()
            ->post($endpoint, [
                'model' => $model,

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Return only the JSON structure requested by the schema.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Return a test response containing the word Gemini in the message field and the number 2 in the number field.',
                    ],
                ],

                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'gemini_connectivity_test',
                        'strict' => true,
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => [
                                    'type' => 'string',
                                ],
                                'number' => [
                                    'type' => 'integer',
                                ],
                            ],
                            'required' => [
                                'message',
                                'number',
                            ],
                            'additionalProperties' => false,
                        ],
                    ],
                ],

                'temperature' => 0,
            ]);

        if ($response->failed()) {
            $this->error('Gemini request failed.');
            $this->line('HTTP status: ' . $response->status());
            $this->line($response->body());

            return self::FAILURE;
        }

        $this->info('Gemini request succeeded.');

        $content = data_get(
            $response->json(),
            'choices.0.message.content'
        );

        $this->line('');
        $this->line('Raw structured response:');
        $this->line((string) $content);

        $data = json_decode((string) $content, true);

        if (!is_array($data)) {
            $this->error('Gemini responded, but the structured JSON could not be decoded.');
            return self::FAILURE;
        }

        $this->line('');
        $this->info('Structured JSON decoded successfully.');

        $this->line('Message: ' . ($data['message'] ?? 'missing'));
        $this->line('Number: ' . ($data['number'] ?? 'missing'));

        if (
            ($data['message'] ?? null) === 'Gemini'
            && ($data['number'] ?? null) === 2
        ) {
            $this->info('SUCCESS: Gemini connectivity + structured output are working.');
            return self::SUCCESS;
        }

        $this->warn('Gemini responded, but the returned values were unexpected.');
        return self::FAILURE;
    }
}