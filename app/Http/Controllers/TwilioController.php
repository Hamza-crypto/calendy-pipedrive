<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Webhooks;
use Exception;
use Illuminate\Http\Request;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Spatie\SlackAlerts\Facades\SlackAlert;

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
            app('log')->channel('twilio_webhook')->info($e->getMessage());
            SlackAlert::message($e->getMessage());
            return ['status' => 'failed', 'sid' => null] ;
        }

    }

    public function sms_status(Request $request)
    {
        $status = $request->MessageStatus;
        if($status == 'sent') return;

        app('log')->channel('twilio_webhook')->info($request->all());

        $sid = $request->MessageSid;

        $task = Task::where('sms_id', $sid)->firstOrFail();
        $task->sms_status = $status;
        if($request->has('ErrorCode')){
            $error_code = $request->ErrorCode;
            if($error_code == '30007'){
                $sms_reason = 'SMS Filtered by Company';
            }
            elseif($error_code == '30003'){
                $sms_reason = 'Unreachable destination handset';
            }
            elseif($error_code == '30006'){
                $sms_reason = 'Landline or unreachable carrier';
            }
            $task->sms_reason = $sms_reason;
        }
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'SMS status updated'
        ]);
    }



}
