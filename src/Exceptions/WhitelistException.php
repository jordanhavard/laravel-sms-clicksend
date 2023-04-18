<?php

namespace JordanHavard\ClickSend\Exceptions;

use Exception;

class WhitelistException extends Exception
{
    public static function numberNotOnWhitelist()
    {
        return new static('The number provided is not on the whitelist',403);
    }

}