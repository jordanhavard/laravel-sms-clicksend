<?php

namespace JordanHavard\ClickSend\Models;

use Illuminate\Support\Facades\Config;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\Exceptions\CouldNotSendNotification;
use JordanHavard\ClickSend\Exceptions\WhitelistException;

class SmsMessage
{
    public static function prepareMessagesArray(array $messages): array
    {
        $allMessages = ['messages' => []];
        $payload = ['messages' => []];
        foreach ($messages as $sms) {
            if (get_class($sms) != ClickSendMessage::class) {
                throw CouldNotSendNotification::notAClickSendMessageObject();
            }

            if (strlen($sms->content) > 1000) {
                throw CouldNotSendNotification::contentLengthLimitExceeded();
            }

            $allMessages['messages'][] = self::prepareMessageObjectWhitelisted($sms);
            $payload['messages'][] = self::prepareMessageObject($sms);
        }

        return [$allMessages, $payload];
    }

    public static function prepareMessageObject(ClickSendMessage $message)
    {
        return [
            'from' => $message->from,
            'to' => self::replaceNumber($message->to),
            'body' => $message->content,
            'schedule' => $message->delay,
            'custom_string' => $message->custom,
        ];

    }

    public static function prepareMessageObjectWhitelisted(ClickSendMessage $message)
    {
        return [
            'from' => $message->from,
            'to' => self::replaceNumber($message->to),
            'body' => $message->content,
            'schedule' => $message->delay,
            'custom_string' => $message->custom,
            'whitelist' => $message->to,
        ];

    }

    public static function replaceNumber($number)
    {
        if (! self::whitelistEnabled()) {
        return $number;
        }

        return ! in_array($number, self::whitelistedNumbers())
            ? self::getWhitelistTestingNumber()
            : $number;
    }

    public static function getWhitelistTestingNumber()
    {
        return Config::get('clicksend.whitelist_testing_number');
    }

    protected static function whitelistedNumbers()
    {
        if (is_null($whitelistedNumbers = Config::get('clicksend.whitelists.'.app()->environment()))) {
            throw WhitelistException::whitelistHasNotBeenSet();
        }

        $numbers = explode(',', $whitelistedNumbers);

        return array_map('trim', $numbers);
    }

    public static function whitelistEnabled()
    {
        return Config::get('clicksend.whitelist_enabled') === true;
    }

    public static function whitelistMessage($number)
    {
        return in_array($number, self::whitelistedNumbers())
            ? Config::get('clicksend.whitelist_allowed')
            : Config::get('clicksend.whitelist_prevented');

    }
}
