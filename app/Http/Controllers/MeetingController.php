<?php

namespace App\Http\Controllers;

use App\Models\EmailSchedule;
use App\Models\EmailSchedules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MeetingController extends Controller
{
    function store(Request $request)
    {
        $data = $request->all();
//        $email = $data['payload']['email'];
        $email = $data['email'];
        $meeting = new \App\Models\Meeting();
        $meeting->email = $email;
        $meeting->save();

//        $event_uuid= $data['payload']['event'];

        $registered_date = Carbon::parse($data['registration']);
        $appointment_date = Carbon::parse($data['meeting_date']);
        echo "Registration:" . $registered_date . "\n";
        echo "Meeting:" . $appointment_date . "\n";

        $diff = $registered_date->diffInDays($appointment_date);
        echo "Diff:" . $diff . "\n";

        $interval = $diff / 4;
        echo "Interval:" . $interval . "\n";

        for ($i = 1; $i <= 4; $i++) {

            $date = $registered_date->addDays($interval);
            $send_date = $date->format('Y-m-d');

            EmailSchedule::updateOrCreate(
                ['meeting_id' => $meeting->id, 'key' => 'email_' . $i],
                ['value' => $send_date]
            );


            echo "Email will be sent at :" . $send_date . "\n";
        }



        return response()->json(['message' => 'Meeting created successfully']);
    }

    function test(){
        //send http request to calendly
        $url = 'https://api.calendly.com/scheduled_events/fff87f68-6542-4e8f-aadc-795aa4651376';
        $response = Http::withToken(env('CALENDLY_TOKEN'))->get($url);
        $data = $response->json();
        $start_time = $data['resource']['start_time'];
        dd($start_time);

    }
}
