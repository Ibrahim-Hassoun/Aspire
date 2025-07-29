<?php

namespace App\Services;

use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class ChatbotServices
{
    public function sendMessage( $message)
    {
       $response = Prism::text()
    ->using(Provider::Gemini, 'gemini-1.5-flash')
    ->withPrompt(
        'Describe what happens in this video',
    )
    ->asText();
return $response; 

    }
}