<?php

namespace App\Services;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;

class AzureEMailService
{
    public function sendMail($to, $subject, $body) {
        $guzzle = new Client();
        $url = 'https://login.microsoftonline.com/' . config('services.azure.tenant_id') . '/oauth2/v2.0/token';
        
        // Get OAuth Token
        $response = $guzzle->post($url, [
            'form_params' => [
                'client_id' => config('services.azure.client_id'),
                'client_secret' => config('services.azure.client_secret'),
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ],
        ]);

        $token = json_decode($response->getBody()->getContents())->access_token;

        $graph = new Graph();
        $graph->setAccessToken($token);

        $mailBody = [
            "message" => [
                "subject" => $subject,
                "body" => [
                    "contentType" => "HTML",
                    "content" => $body
                ],
                "toRecipients" => [
                    [
                        "emailAddress" => [
                            "address" => $to
                        ]
                    ]
                ]
            ]
        ];

        return $graph->createRequest("POST", "/users/" . config('services.azure.sender') . "/sendMail")
            ->attachBody($mailBody)
            ->execute();
			
			//testomg
    }
}