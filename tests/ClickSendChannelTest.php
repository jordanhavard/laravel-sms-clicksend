<?php

namespace JordanHavard\ClickSend\Test;

use Exception;
use Illuminate\Events\Dispatcher;
use JordanHavard\ClickSend\ClickSendApi;
use JordanHavard\ClickSend\ClickSendChannel;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\Exceptions\CouldNotSendNotification;
use Mockery;

class ClickSendChannelTest extends TestCase
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

        $this->smsc = Mockery::mock(new ClickSendApi('username', 'APIKEY'));
        $this->channel = new ClickSendChannel($this->smsc, new Dispatcher());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->smsc->shouldReceive('sendSms')
            ->once()
            ->andReturn([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'from' => 'Test Suite',
                    'to' => '+61422222222',
                    'body' => 'Message content',
                    'schedule' => null,
                    'custom_string' => null,
                ],
            ]);

        $this->channel->send(
            new TestNotifiable(), new TestNotification()
        );
    }

    /** @test */
    public function it_throws_an_exception_if_result_is_not_set()
    {
        $this->smsc->shouldReceive('sendSms')
            ->once()
            ->andReturn([
                'message' => 'Some error',
            ]);

        $this->smsc->shouldReceive('getResponse')
            ->once()
            ->andReturn((object) [
                'success' => false,
                'message' => 'Some error',
                'data' => (object) [
                    'messages' => [
                        (object) [
                            'message_id' => 'foobar',
                        ],
                    ],
                ],
            ]);

        $this->expectException(Exception::class);

        $this->channel->send(
            new TestNotifiable(), new TestNotification()
        );
    }

    /** @test */
    public function it_throws_exception_if_unexpected_object_passed_to_sendManySms()
    {
        $this->expectException(CouldNotSendNotification::class);

        $messages = [(object) ['to' => '1234567890', 'message' => 'test']];

        $this->smsc->sendManySms(
            $messages
        );
    }

    /** @test */
    public function it_throws_exception_if_too_many_objects_passed_to_sendManySms()
    {
        $this->expectException(CouldNotSendNotification::class);

        $messages = [];
        for ($c = 0; $c < 1001; $c++) {
            $messages[] = new ClickSendMessage();
        }

        $this->smsc->sendManySms(
            $messages
        );
    }
}

class TestNotifiable
{
    public function routeNotificationForClicksend()
    {
        return '+61411111111';
    }
}

class TestNotifiableWithoutRouteNotificationForSmscru extends TestNotifiable
{
    public function routeNotificationForClicksend()
    {
        return false;
    }
}

class TestNotification extends \Illuminate\Notifications\Notification
{
    public function toClickSend()
    {
        return (new ClickSendMessage('messageContent'))->from('fromNumber');
    }
}
