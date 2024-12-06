<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    private $sid;
    private $authToken;
    private $twilioPhoneNumber;

    public function __construct()
    {
        $this->sid = env('TWILIO_SID');
        $this->authToken = env('TWILIO_AUTH_TOKEN');
        $this->twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');
    }

    public function sendSms($phoneNumber, $message)
    {
        $client = new Client($this->sid, $this->authToken);

        Log::info('Sending SMS to phone number: ' . $phoneNumber);
        
        try {
            $client->messages->create(
                $phoneNumber,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $message
                ]
            );
            Log::info('SMS sent successfully to ' . $phoneNumber);
        } catch (\Exception $e) {
            Log::error('SMS sending failed to ' . $phoneNumber . ': ' . $e->getMessage());
        }
    }
}