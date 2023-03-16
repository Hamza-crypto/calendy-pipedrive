<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Task;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        $stages = Stage::select('stage_id', 'name')->get();
        return view('tasks.index', compact('stages'));
    }

    public function store(Request $request)
    {
        $person_id = $request->current['person_id'];

        $current_stage = $request->current['stage_id'];
        $previous_stage = $request->previous['stage_id'];

        if($current_stage == $previous_stage){
            return;
        }

        $stage = Stage::where('stage_id', $current_stage)->firstOrFail();

        $pipedrive = new PipedriveController();
        $person = $pipedrive->find_person($person_id);


//        if($person['email'] != env('MAIL_FROM_ADDRESS')){
//            return;
//        }

        $task = new Task();
        $task->email = $person['email'];
        $task->phone = $person['phone'];
        $task->stage = $stage->name;
        $task->save();

        if($stage->sms){
            $twilio = new TwilioController();
            $response = $twilio->send_sms($person['phone'], $stage->sms);
            if($response['status'] == 'queued'){
                $task->sms_status = 'queued';
                $task->sms_id = $response['sid'];
                $task->save();
            }
        }

        if($stage->voice){
            $voicemail = new VoicemailController();
            $voicemail->send_vm($person['phone'], $stage->voice, $task->id);

            $task->vm_status = 'queued';
            $task->save();
        }

    }

    public function person_created(Request $request)
    {
        $email = $request->current['primary_email'];
        $phone = $request->current['phone'][0]['value'];

        $pipedrive = new PipedriveController();
        $phone = $pipedrive->formatPhoneNumberToE164($phone);
//        if(strpos($email, 'mailinator.com') == false){
//            return;
//        }

        $stage = Stage::where('stage_id', 31)->firstOrFail(); //31 is the stage id for 'New Clients - First Contact'

        $task = new Task();
        $task->email = $email;
        $task->phone = $phone;
        $task->stage = $stage->name;
        $task->save();

        if($stage->sms){
            $twilio = new TwilioController();
            $response = $twilio->send_sms($phone, $stage->sms);
            if($response['status'] == 'queued'){
                $task->sms_status = 'queued';
                $task->sms_id = $response['sid'];
                $task->save();
            }
        }

        if($stage->voice){
            $voicemail = new VoicemailController();
            $voicemail->send_vm($phone, $stage->voice, $task->id);

            $task->vm_status = 'queued';
            $task->save();
        }

    }


    public function facebook(Request $request)
    {
        app()->log->info($request->all());
        return $request->hub_challenge;
    }
}
