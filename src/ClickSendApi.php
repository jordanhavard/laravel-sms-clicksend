<?php
/**
 * Click Send API using ClickSend API wrapper
 *
 * @url https://github.com/ClickSend/clicksend-php
 */

namespace JordanHavard\ClickSend;

use Exception;
use JordanHavard\ClickSend\Events\SmsRequestSent;
use JordanHavard\ClickSend\Exceptions\APIException;
use JordanHavard\ClickSend\Exceptions\CouldNotSendNotification;
use JordanHavard\ClickSend\Models\SmsMessage;

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
        $this->client = new ClickSendClient($username, $api_key);

    }

    public function sendSms($from, $to, $message, $custom = null, $delay = null)
    {
        $sms = (new ClickSendMessage($message))->from($from)->to($to)->delay($delay)->custom($custom);

        $return = $this->sendManySms([$sms]);

        $return['data'] = $return['data']['messages'][0];

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

        [$allMessages,$payload] = SmsMessage::prepareMessagesArray($messages);

        $result = [
            'success' => false,
            'message' => '',
            'data' => $allMessages,
            'whitelistEnabled' => SmsMessage::whitelistEnabled(),
            'failures' => [], // key value pair of <error, ClickSendMessage[]>
        ];

        try {
            $this->response = $this->client->getSMS()->sendSms($payload);
            broadcast(new SmsRequestSent($this->response));
            // checked how many got through
            $worked = 0;
            foreach ($this->response->data->messages as $key => $message_response) {
                $allMessages['messages'][$key]['api_response'] = $this->response->response_msg;
                $allMessages['messages'][$key]['message_id'] = $message_response->message_id;

                if ($message_response->status == 'SUCCESS') {
                    $worked++;

                    if (SmsMessage::whitelistEnabled()) {
                        $allMessages['messages'][$key]['api_response'] = SmsMessage::whitelistMessage(
                            $allMessages['messages'][$key]['whitelist']
                        ).$this->response->response_msg;
                        $allMessages['messages'][$key]['to'] = $allMessages['messages'][$key]['whitelist'];
                    }
                    unset($allMessages['messages'][$key]['whitelist']);

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
                $result['message'] = count($messages).' of '.count($messages).' '.strtolower($this->response->response_msg);
            } else {
                $result['message'] = (count($messages) - $worked).' of '.count($messages).' messages failed to send';
            }
        }
        // clicksend API error
        catch (APIException|Exception $exception) {
            $result['message'] = $exception->getMessage();
        }

        $result['data'] = $allMessages;

        return $result;
    }
}
