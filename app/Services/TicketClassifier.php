<?php

namespace App\Services;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        if (env('OPENAI_CLASSIFY_ENABLED', false) == false) {
            return [
                'category' => fake()->randomElement(['billing', 'technical', 'account', 'general']),
                'explanation' => 'Manual classification (AI disabled)',
                'confidence' => fake()->randomFloat(2, 0.5, 1.0),
            ];
        }

        $prompt = "Classify the support ticket below. Return JSON with keys: category, explanation, confidence (0-1). Ticket Subject: {$subject} Ticket Body: {$body}";

        $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [['role' => 'system', 'content' => $prompt]],
        ]);

        $parsed = json_decode($response->choices[0]->message->content ?? '{}', true);

        return [
            'category' => $parsed['category'] ?? 'general',
            'explanation' => $parsed['explanation'] ?? 'No explanation',
            'confidence' => $parsed['confidence'] ?? 0.0,
        ];
    }
}
