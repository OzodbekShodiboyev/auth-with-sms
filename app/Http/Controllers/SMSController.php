<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SMSController extends Controller
{
    public function sendSMS()
    {
        $client = new Client();
        $authUrl = 'https://notify.eskiz.uz/api/auth/login';
        $smsUrl = 'https://notify.eskiz.uz/api/sms/send';

        $authData = [
            'email' => 'ozodbekonline1@gmail.com',
            'password' => 'rxM2y4kO3rXbTzflpnZWgXFg9CjVyMUOAfeC9Y04'
        ];

        try {
            // Authenticate and retrieve the bearer token
            $authResponse = $client->request('POST', $authUrl, [
                'form_params' => $authData
            ]);

            $authStatusCode = $authResponse->getStatusCode();

            if ($authStatusCode !== 200) {
                throw new \Exception('Authentication failed. Status Code: ' . $authStatusCode);
            }

            $authBody = $authResponse->getBody();
            $authData = json_decode($authBody, true);

            if (!isset($authData['token'])) {
                throw new \Exception('Invalid response. Token not found. Response: ' . $authBody);
            }

            $token = $authData['token'];

            // Prepare the SMS data
            $smsData = [
                // Fill in the necessary data for sending the SMS
            ];

            // Send the SMS
            $response = $client->request('POST', $smsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $smsData
            ]);

            $smsStatusCode = $response->getStatusCode();

            if ($smsStatusCode !== 200) {
                throw new \Exception('SMS sending failed. Status Code: ' . $smsStatusCode);
            }

            $smsResponse = $response->getBody();
            echo $smsResponse;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
