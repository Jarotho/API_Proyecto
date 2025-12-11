<?php

namespace App\Services;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

class SnsService
{
    protected $client;
    protected $topicArn;

    public function __construct()
    {
        $this->client = new SnsClient([
            'version' => '2010-03-31',
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $this->topicArn = env('SNS_TOPIC_ARN');
    }

    public function publish(string $subject, string $message): bool
    {
        try {
            $this->client->publish([
                'TopicArn' => $this->topicArn,
                'Subject'  => $subject,
                'Message'  => $message,
            ]);

            return true;
        } catch (AwsException $e) {
            // Aquí puedes loguear el error si quieres
            \Log::error('Error enviando SNS: '.$e->getMessage());
            return false;
        }
    }
}
