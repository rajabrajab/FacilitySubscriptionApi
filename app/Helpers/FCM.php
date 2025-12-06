<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use stdClass;

class FCM
{
    private $to;
    private $data_type;
    private $notification_type;
    private $userId;
    private $response;
    private $data = [];
    private $replacements = [];

    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    public function userId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function notificationType($notification_type)
    {
        $this->notification_type = $notification_type;
        return $this;
    }

    public function dataType($data_type)
    {
        $this->data_type = $data_type;
        return $this;
    }

    public function replacements(array $replacements)
    {
        $this->replacements = $replacements;
        return $this;
    }

    public function data(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function send()
    {
        $template = config("notificationContent.{$this->data_type}");

        if (!$template) {
            throw new \Exception("Notification template not found for type: {$this->data_type}");
        }

        $localizedTitle = [
            'en' => $this->replacePlaceholders($template['title']['en'], $this->replacements),
            'ar' => $this->replacePlaceholders($template['title']['ar'], $this->replacements)
        ];

        $localizedMessage = [
            'en' => $this->replacePlaceholders($template['message']['en'], $this->replacements),
            'ar' => $this->replacePlaceholders($template['message']['ar'], $this->replacements)
        ];

        $user = User::find($this->userId);
        $userLanguage = $user->language ?? 'en';

        $dataPayload = $this->prepareDataPayload($template['data'] ?? [], $this->replacements);
        $dataPayload['title'] = $localizedTitle[$userLanguage];
        $dataPayload['body'] = $localizedMessage[$userLanguage];
        $dataPayload['notificationType'] = $this->notification_type;



        $client = new GoogleClient();
        $client->setAuthConfig(Storage::disk('private')->path('json/snjallrbookingsystem-149d5-firebase-adminsdk-fbsvc-9bebcfceff.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();
        $accessToken = $token['access_token'] ?? null;

        if (!$accessToken) {
            $this->response = ['error' => 'Unable to get access token.'];
            return $this;
        }

        $projectId = json_decode(file_get_contents(Storage::disk('private')->path('json/snjallrbookingsystem-149d5-firebase-adminsdk-fbsvc-9bebcfceff.json')), true)['project_id'];

        $payload = [
            'message' => [
                'token' => $this->to,
                'notification' => new stdClass,
                'data' => $dataPayload
            ],
        ];

        // $dbDatapayload = [
        //     'type' => $dataPayload['type'],
        //     'route' => $dataPayload['route'],
        //     'route_value' => $dataPayload['route_value']
        // ];

        $this->response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload)
            ->json();

        // Notification::create([
        //     'user_id' => $this->userId,
        //     'title' => $localizedTitle,
        //     'body' => $localizedMessage,
        //     'data' => $dbDatapayload,
        //     'notification_type' => $this->notification_type ?? 'message',
        //     'read' => false,
        // ]);

        return $this;
    }

    private function replacePlaceholders($text, $replacements)
    {
        foreach ($replacements as $key => $value) {
            $text = str_replace(":{$key}", $value, $text);
        }
        return $text;
    }

    private function prepareDataPayload($templateData, $replacements)
    {
        $payload = [];

        foreach ($templateData as $key => $value) {
            $payload[$key] = $this->replacePlaceholders($value, $replacements);
        }

        return array_merge($payload, $this->data);
    }

    public function response()
    {
        return $this->response;
    }
}
