<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Support\Facades\Config;
use JordanHavard\ClickSend\ClickSendServiceProvider;

class WhitelistingTest extends TestCase
{

    /** @test */
    public function it_checks_if_the_whitelist_is_disabled_by_default()
    {
        $this->assertFalse(Config::get('clicksend.whitelist_enabled'));
    }

}