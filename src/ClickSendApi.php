<?php
/**
 * Click Send API using ClickSend API wrapper
 *
 * @url https://github.com/ClickSend/clicksend-php
 */

namespace NotificationChannels\ClickSend;

use NotificationChannels\ClickSend\Exceptions\CouldNotSendNotification;
use ClickSendLib\ClickSendClient;
use ClickSendLib\APIException;

class ClickSendApi
{
    /** @var ClickSendClient client */
    protected $client;

    /** @var string */
    protected $username;

    /** @var string */
    protected $api_key;

    public function __construct($username, $api_key)
    {
        $this->username = $username;
        $this->api_key  = $api_key;

        // Prepare ClickSend client
        try {
            $this->client = new ClickSendClient($username, $api_key);
        }
        catch(APIException $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithClicksend($exception);
        }

        // Client may get instances e.g. getSms(), getVoice(), getAccount(), getCountries() .....
        // $this->client->getSMS();
    }

    /**
     * @param $from
     * @param $to
     * @param $message
     * @param null $delay
     *
     * @return array
     */
    public function sendSms($from, $to, $message, $custom = null, $delay = null)
    {
        // The payload may have more messages but we use just one at a time
        $payload = ['messages' => [
            [
                "from"      => $from,
                "to"        => $to,
                "body"      => $message,
                "schedule"  => $delay,
                "custom_string"  => $custom,
            ]
        ]];

        $result = [
            'success' => false,
            'message' => '',
            'data'    => $payload['messages'][0],
        ];

        try {
            $response = $this->client->getSMS()->sendSms($payload);

            // communication error
            if($response->response_code != 'SUCCESS') {
                $result['message'] = $response->response_code;
            }
            // sending error
            elseif ($response->data->messages[0]->status != 'SUCCESS') {
                $result['message'] = $response->data->messages[0]->status;
            }
            else {
                $result['success'] = true;
                $result['message'] = 'Message sent successfully.';
            }

        }
        // clicksend API error
        catch (APIException $exception) {
            $result['message'] = $exception->getReason();
        }
        // any php error
        catch (\Exception $exception) {
            $result['message'] = $exception->getMessage();
        }

        return $result;
    }


    /**
     * Return Client for accessing all other api functions
     *
     * @return ClickSendClient
     */
    public function getClient()
    {
        return $this->client;
    }

}
