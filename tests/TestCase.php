<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Events\Dispatcher;
use JordanHavard\ClickSend\ClickSendApi;
use JordanHavard\ClickSend\ClickSendChannel;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\ClickSendServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var ClickSendApi
     */
    private $smsc;

    /**
     * @var ClickSendMessage
     */
    private $message;

    /**
     * @var ClickSendChannel
     */
    private $channel;

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

    protected function sendSms()
    {
        return $this->smsc->sendSms(
            'Test suite',
            '+61411111111',
            'This is a test 555'
        );
    }
}
