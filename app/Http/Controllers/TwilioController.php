<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioController extends Controller
{

    public function send_sms($phone = "+12068133607", $message = "Hello World")
    {
        try{
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

            $response = $twilio->messages->create(
                $phone,
                [
                    'from' => env('TWILIO_FROM'),
                    'body' => $message,
                    'statusCallback' => env('APP_URL') . '/sms_status'
                ]
            );

            return ['status' => $response->status, 'sid' => $response->sid] ;
        }
        catch (Exception $e){

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function sms_status(Request $request)
    {
        $sid = $request->MessageSid;
        $status = $request->MessageStatus;

        $task = Task::where('sms_id', $sid)->firstOrFail();
        $task->sms_status = $status;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'SMS status updated'
        ]);
    }

}
