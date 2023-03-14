<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PipedriveController extends Controller
{
    public string $base_url = 'https://api.pipedrive.com/v1';

    public function find_person($id)
    {
        $response = Http::get(sprintf('%s/persons/%d?api_token=%s', $this->base_url, $id, env('PIPEDRIVE_API_TOKEN')));
        $response = $response->json();

        $email = $response['data']['primary_email'];
        $phone= $response['data']['phone'][0]['value'];

        return [
            'email' => $email,
            'phone' => $phone
        ];
    }

}
