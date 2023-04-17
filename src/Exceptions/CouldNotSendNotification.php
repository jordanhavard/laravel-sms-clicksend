<?php

namespace JordanHavard\ClickSend\Exceptions;

use ClickSendLib\APIException;
use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    /**
     * Thrown when mesage status is not SUCCESS
     *
     *
     * @return static
     */
    public static function clicksendRespondedWithAnError(APIException $exception)
    {
        return new static(
            "Notification Error: {$exception->getMessage()}"
        );
    }

    /**
     * Thrown when we're unable to communicate with Clicksend.com
     *
     *
     * @return static
     */
    public static function couldNotCommunicateWithClicksend(Exception $exception)
    {
        return new static("Notification Gateway Error: {$exception->getReason()} [{$exception->getCode()}]");
    }

    /**
     * Thrown when you try to send too many bulk SMS'. ClickSend API limits messages to 1000
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function tooManyBulkSMSMessages()
    {
        return new static("You cannot send more than 1000 SMS' in bulk");
    }

    /**
     * Thrown when you try to include an object that is not of Type ClickSendMessage to the $messages array of ClickSendApi::sendManySms(...)
     *
     * @param  Exception  $exception
     * @return static
     */
    public static function notAClickSendMessageObject()
    {
        return new static("Each message object must be of type NotificationChannels\ClickSend\ClickSendMessage");
    }
}
