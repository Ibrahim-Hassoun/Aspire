<?php

namespace App\Http\Controllers;

use App\Services\ChatbotServices;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    use ApiResponse;

    protected $chabotServices;

    public function __construct(ChatbotServices $chabotServices)
    {
        $this->chabotServices = $chabotServices;
    }
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $response = $this->chabotServices->sendMessage($request['message']);
            
            return $this->success('Chatbot replied successfully', [
                'message' => $response,
                'user_message' => $request['message'],
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Test chatbot connection
     */
    public function testConnection()
    {
        try {
            $result = $this->chabotServices->testConnection();
            
            if ($result['success']) {
                return $this->success('Chatbot connection test passed', $result);
            } else {
                return $this->error('Chatbot connection test failed', 500, $result);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Simple test endpoint
     */
    public function simpleTest()
    {
        try {
            $response = $this->chabotServices->simpleTest();
            
            return $this->success('Simple test completed', [
                'response' => $response,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
