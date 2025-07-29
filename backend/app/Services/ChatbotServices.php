<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ChatbotServices
{
    /**
     * Send message to Gemini AI using direct HTTP approach
     */
    public function sendMessage($message)
    {
        try {
            // Validate input
            if (empty(trim($message))) {
                return "Please provide a message for me to respond to.";
            }

            Log::info('Chatbot request initiated', [
                'message_length' => strlen($message),
                'user_id' => auth()->id() ?? 'guest'
            ]);

            // Use direct HTTP method (most reliable)
            $response = $this->sendWithDirectHttp($message);
            
            if ($response) {
                Log::info('Chatbot response generated successfully');
                return $response;
            }

            // If method fails, return helpful message
            return $this->getFallbackResponse();

        } catch (\Exception $e) {
            Log::error('Chatbot service error', [
                'message' => $message,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->getFallbackResponse();
        }
    }

    /**
     * Send message using direct HTTP call to Gemini API
     */
    private function sendWithDirectHttp($message)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (empty($apiKey)) {
                Log::error('Gemini API key not found in environment');
                return null;
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

            // Build the complete prompt
            $systemPrompt = $this->buildSystemPrompt();
            $fullPrompt = $systemPrompt . "\n\nUser: " . trim($message) . "\n\nAssistant:";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            Log::info('Making request to Gemini API', [
                'url' => $url,
                'prompt_length' => strlen($fullPrompt)
            ]);

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 10,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                ]
            ])->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Gemini API response received', [
                    'status' => $response->status(),
                    'has_candidates' => isset($data['candidates'])
                ]);

                if (isset($data['candidates']) && count($data['candidates']) > 0) {
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    
                    if ($text) {
                        return trim($text);
                    }
                }

                Log::warning('No valid response content from Gemini API', ['response_data' => $data]);
                return null;
            } else {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Direct HTTP method failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Build system prompt for inventory management context
     */
    private function buildSystemPrompt()
    {
        return "You are an AI assistant for an inventory management system. You help users with:\n" .
            "- Product information and queries\n" .
            "- Inventory levels and stock management\n" .
            "- General system assistance\n" .
            "- Data interpretation and insights\n\n" .
            "Be helpful, professional, and concise. If you don't have specific data about their inventory, " .
            "provide general guidance or suggest where the user might find the information in their system.";
    }

    /**
     * Get fallback response when AI is unavailable
     */
    private function getFallbackResponse()
    {
        $responses = [
            "I'm currently experiencing technical difficulties connecting to the AI service. Please try again in a few moments.",
            "Sorry, I'm temporarily unavailable. You can try refreshing the page or contacting support if the issue persists.",
            "I'm having trouble processing your request right now. Please try again later or check your internet connection."
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Test connection to Gemini API
     */
    public function testConnection()
    {
        try {
            $response = $this->sendMessage("Hello, can you hear me?");
            $isSuccessful = !str_contains($response, 'technical difficulties');
            
            return [
                'success' => $isSuccessful,
                'response' => $response,
                'timestamp' => now(),
                'api_key_configured' => !empty(env('GEMINI_API_KEY')),
                'ssl_settings' => [
                    'verify_peer' => config('http.ssl.verify_peer', false),
                    'verify_host' => config('http.ssl.verify_host', false)
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now(),
                'api_key_configured' => !empty(env('GEMINI_API_KEY'))
            ];
        }
    }

    /**
     * Simple method for testing basic functionality
     */
    public function simpleTest()
    {
        try {
            return $this->sendWithDirectHttp("Say hello");
        } catch (\Exception $e) {
            Log::error('Simple test failed', ['error' => $e->getMessage()]);
            return "Test failed: " . $e->getMessage();
        }
    }
}