<?php

namespace App\Service;

use Twilio\Rest\Client;

class SendSmsService
{
    private $accountSid;
    private $authToken;
    private $fromNumber;
    private $client;

    public function setAccountSid(string $accountSid): void
    {
        $this->accountSid = $accountSid;
    }

    public function setAuthToken(string $authToken): void
    {
        $this->authToken = $authToken;
    }

    public function setFromNumber(string $fromNumber): void
    {
        $this->fromNumber = $fromNumber;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function send(string $to, string $message): void
    {
        $this->client->messages->create(
            $to,
            [
                'from' => $this->fromNumber,
                'body' => $message,
            ]
        );
    }
}


