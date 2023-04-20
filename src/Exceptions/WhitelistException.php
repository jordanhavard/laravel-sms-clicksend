<?php

namespace JordanHavard\ClickSend\Exceptions;

use Exception;

class WhitelistException extends Exception
{
    public static function whitelistHasNotBeenSet()
    {
        return new static('No whitelist found for current environment',422);
    }
}
