<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Events\Dispatcher;
use JordanHavard\ClickSend\ClickSendApi;
use JordanHavard\ClickSend\ClickSendChannel;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\ClickSendServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    //    protected $enablesPackageDiscoveries = true;

    /**
     * @var ClickSendApi
     */
    protected $smsc;

    /**
     * @var ClickSendMessage
     */
    protected $message;

    /**
     * @var ClickSendChannel
     */
    protected $channel;

    public function setUp(): void
    {
        parent::setUp();

        $this->smsc = new ClickSendApi('username', 'apikey');
        $this->channel = new ClickSendChannel($this->smsc, new Dispatcher());
    }

    protected function getPackageProviders($app): array
    {
        return [
            ClickSendServiceProvider::class,
        ];
    }

    protected function sendSms(
        $message = 'This is a message',
        $to = '+61411111111',
        $from = 'Test suite',
    ) {
        return $this->smsc->sendSms($from, $to, $message);
    }
}
