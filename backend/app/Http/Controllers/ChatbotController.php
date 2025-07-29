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
            $response=$this->chabotServices->sendMessage($request['message']);
            return $this->success( 'chatbot replied successfully',$response);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
