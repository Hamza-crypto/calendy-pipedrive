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
        $phone = $response['data']['phone'][0]['value'];

        return [
            'email' => $email,
            'phone' => $this->formatPhoneNumberToE164($phone)
        ];
    }

    function formatPhoneNumberToE164($phoneNumber, $defaultCountryCode = '1')
    {
        // Remove all non-numeric characters from the phone number.
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If the phone number starts with a plus sign, remove it.
        if (strpos($phoneNumber, '+') === 0) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        // If the phone number doesn't start with the default country code, add it.
        if (strpos($phoneNumber, $defaultCountryCode) !== 0) {
            $phoneNumber = $defaultCountryCode . $phoneNumber;
        }

        // Return the phone number in E.164 format.
        return '+' . $phoneNumber;
    }

}
