<?php
/**
 * Click Send API using ClickSend API wrapper
 *
 * @url https://github.com/ClickSend/clicksend-php
 */

namespace JordanHavard\ClickSend;

use ClickSendLib\APIException;
use ClickSendLib\ClickSendClient;
use JordanHavard\ClickSend\Exceptions\CouldNotSendNotification;

class ClickSendApi
{
    const MAX_PER_CALL = 1000;

    /** @var ClickSendClient client */
    protected $client;

    /** @var string */
    protected $username;

    /** @var string */
    protected $api_key;

    /** @var object */
    protected $response;

    public function __construct($username, $api_key)
    {
        $this->username = $username;
        $this->api_key = $api_key;

        // Prepare ClickSend client
        try {
            $this->client = new ClickSendClient($username, $api_key);
        } catch (APIException $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithClicksend($exception);
        }

    }

    public function sendSms($from, $to, $message, $custom = null, $delay = null)
    {
        $sms = (new ClickSendMessage($message))->from($from)->to($to)->delay($delay)->custom($custom);

        $return = $this->sendManySms([$sms]);
        // Keep the same output signature as previous iterations of sendSms(...) so we don't
        // introduce breaking changes
        $return['data'] = $return['data'][0];
        if ($return['success']) {
            $return['message'] = 'Message sent successfully.';
        }
        unset($return['failures']);

        return $return;
    }

    /**
     * @param  array  $messages - array of ClickSendMessage to send
     * @return array
     */
    public function sendManySms(array $messages)
    {
        if (count($messages) > self::MAX_PER_CALL) {
            throw CouldNotSendNotification::tooManyBulkSMSMessages();
        }

        $payload = ['messages' => []];
        foreach ($messages as $sms) {
            if (get_class($sms) != ClickSendMessage::class) {
                throw CouldNotSendNotification::notAClickSendMessageObject();
            }

            $payload['messages'][] = [
                'from' => $sms->from,
                'to' => $sms->to,
                'body' => $sms->content,
                'schedule' => $sms->delay,
                'custom_string' => $sms->custom,
            ];
        }
        
        $result = [
            'success' => false,
            'message' => '',
            'data' => $payload['messages'],
            'failures' => [], // key value pair of <error, ClickSendMessage[]>
        ];

        try {
            $this->response = $this->client->getSMS()->sendSms($payload);
            // communication error
            if ($this->response->response_code != 'SUCCESS') {
                $result['message'] = $this->response->response_code;
            } else {
                // checked how many got through
                $worked = 0;
                foreach ($this->response->data->messages as $message_response) {
                    if ($message_response->status == 'SUCCESS') {
                        $worked++;
                    } else {
                        // populate the message value for the first error only to
                        // prevent breaking changes
                        if ($result['message'] == '') {
                            $result['message'] = $message_response->status;
                        }

                        if (! isset($result['failures'][$message_response->status])) {
                            $result['failures'][$message_response->status] = [];
                        }
                        $result['failures'][$message_response->status][] =
                            (new ClickSendMessage($message_response->body))
                            ->to($message_response->to);
                    }
                }
                if ($worked == count($messages)) {
                    $result['success'] = true;
                    $result['message'] = 'Messages sent successfully.';
                }
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

    /**
     * Return the response from the client request
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }
}
