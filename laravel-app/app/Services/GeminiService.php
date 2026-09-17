<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY', ''));
        $this->model = 'gemini-2.5-flash';
    }

    /**
     * Generate content with Gemini API.
     */
    public function generate(string $prompt, string $systemInstruction = ''): string
    {
        if (empty($this->apiKey)) {
            return 'کلید اتصال به جمینای (GEMINI_API_KEY) در فایل .env تنظیم نشده است.';
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        if (!empty($systemInstruction)) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'پاسخی دریافت نشد.';
        }

        return 'خطا در برقراری ارتباط با جمینای: ' . $response->body();
    }
}
