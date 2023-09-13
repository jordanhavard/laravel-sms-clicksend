<?php

namespace JordanHavard\ClickSend;

class ClickSendSubaccount
{
    public $api_username;

    public $password;

    public $email;

    public $phone_number;

    public $first_name;

    public $last_name;

    public $access_users = 0;

    public $access_billing = 0;

    public $access_reporting = 0;

    public $access_contacts = 0;

    public $access_settings = 0;

    public $access_sms = 0;

    public $access_email = 0;

    public $access_voice = 0;

    public $access_fax = 0;

    public $access_post = 0;

    public $access_reseller = 0;

    public $access_mms = 0;

    public $share_campaigns = 0;

    public $note = '';

    public function ready()
    {
        return ! (is_null($this->api_username) ||
            is_null($this->password) ||
            is_null($this->phone_number) ||
            is_null($this->first_name) ||
            is_null($this->last_name)
        );
    }
}
