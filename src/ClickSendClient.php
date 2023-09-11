<?php

namespace JordanHavard\ClickSend;

use JordanHavard\ClickSend\Controllers\SMSController;
use JordanHavard\ClickSend\Controllers\SubaccountController;

class ClickSendClient extends \ClickSendLib\ClickSendClient
{
    /**
     * Constructor with authentication and configuration parameters
     */
    public function __construct(
        $username = null,
        $key = null
    ) {
        parent::__construct($username, $key);
    }

    /**
     * Singleton access to SMS controller
     *
     * @return SMSController The *Singleton* instance
     */
    public function getSMS()
    {
        return SMSController::getInstance();
    }

    public function getSubaccounts()
    {
        return SubaccountController::getInstance();
    }
}
