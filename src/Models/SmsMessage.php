<?php

namespace JordanHavard\ClickSend\Models;

use Illuminate\Support\Facades\Config;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\Exceptions\WhitelistException;
use function app;
use function array_map;
use function explode;
use function in_array;

class SmsMessage
{

    public static function canSendToThisNumber(ClickSendMessage $message)
    {
        $whitelistedNumbers = self::whitelistedNumbers();
        return in_array($message->to,$whitelistedNumbers)
            ? true
            : throw WhitelistException::numberNotOnWhitelist();

    }

    protected static function whitelistedNumbers()
    {
        $numbers = explode(',',Config::get('clicksend.whitelists.'.app()->environment()));
        return array_map('trim',$numbers);
    }

}