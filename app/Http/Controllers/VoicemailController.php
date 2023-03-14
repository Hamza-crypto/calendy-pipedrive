<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Webhooks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoicemailController extends Controller
{
    function send_vm($phone, $recording_id, $foreign_id)
    {
        $data = [
            'team_id' => env('DROP_COWBOY_TEAM_ID'),
            'secret' => env('DROP_COWBOY_SECRET'),
            'brand_id' => env('DROP_COWBOY_BRAND_ID'),
            'pool_id' => env('DROP_COWBOY_POOL_ID'),

            'phone_number' => $phone,
            'forwarding_number' => env('DEREK_PHONE'),

            'media_id' => $recording_id,
            'foreign_id' => $foreign_id,
            'callback_url' => env('APP_URL') . '/vm_status',
        ];

        $response = Http::asJson()->post("https://api.dropcowboy.com/v1/rvm", $data);
        app('log')->channel('vm_webhook')->info($response->json());
    }

    function vm_status(Request $request)
    {
        Webhooks::create([
            'task_id' => $request->foreign_id,
            'webhook_response' => json_encode($request->all())
        ]);

        app('log')->channel('vm_webhook')->info($request->all());
        $id = $request->foreign_id;
        Task::find($id)->update([
            'vm_status' => $request->status,
            'vm_reason' => $request->reason,
        ]);
    }
}
