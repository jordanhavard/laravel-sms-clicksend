<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Support\Facades\Config;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\ClickSendServiceProvider;
use JordanHavard\ClickSend\Exceptions\NumberNotOnWhitelistException;
use JordanHavard\ClickSend\Exceptions\WhitelistException;
use JordanHavard\ClickSend\Models\SmsMessage;

class WhitelistingTest extends TestCase
{

    /** @test */
    public function it_checks_if_the_whitelist_is_disabled_by_default()
    {
        $this->assertFalse(Config::get('clicksend.whitelist_enabled'));
    }

    /** @test */
    public function it_can_send_to_a_number_on_the_whitelist()
    {
        Config::set('clicksend.whitelists.testing','+6155555555');
        $message = (new ClickSendMessage())->to('+6155555555');

        $this->assertTrue(SmsMessage::canSendToThisNumber($message));
    }

    /** @test */
    public function it_throws_exception_if_number_is_not_on_whitelist()
    {
        $this->expectExceptionMessage('The number provided is not on the whitelist');
        $message = (new ClickSendMessage())->to('+6155555555');

        SmsMessage::canSendToThisNumber($message);
    }

}