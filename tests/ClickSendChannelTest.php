<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JordanHavard\ClickSend\ClickSendApi;
use JordanHavard\ClickSend\ClickSendChannel;
use JordanHavard\ClickSend\ClickSendMessage;
use JordanHavard\ClickSend\Exceptions\CouldNotSendNotification;
use Mockery;
use stdClass;

class ClickSendChannelTest extends TestCase
{
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
        Http::fake([
            '*' => Http::response([
                'http_code' => 200,
                'response_code' => 'SUCCESS',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'SUCCESS',
                            'message_id' => Str::uuid(),
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->channel->send(
            new TestNotifiable(), new TestNotification()
        );
        $this->assertTrue($response['success']);
    }

    /** @test */
    public function it_can_send_a_notification_from_string()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 200,
                'response_code' => 'SUCCESS',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'SUCCESS',
                            'message_id' => Str::uuid(),
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->channel->send(
            new TestNotifiable(), new TestNotificationWithStringMessage()
        );
        $this->assertTrue($response['success']);
    }

    /** @test */
    public function it_can_throw_notification_error()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 403,
                'response_code' => 'ERROR',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'ERROR',
                            'message_id' => Str::uuid(),

                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->channel->send(
            new TestNotifiable(), new TestNotificationWithStringMessage()
        );
        $this->assertFalse($response['success']);
    }

    /** @test */
    public function it_returns_failures_if_there_are_some()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 200,
                'response_code' => 'SUCCESS',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'SUCCESS',
                            'body' => 'testing message for success',
                            'to' => '+61422222222',
                            'message_id' => Str::uuid(),
                        ],
                        [
                            'status' => 'FAILED',
                            'body' => 'testing message for failure',
                            'to' => '+61433333333',
                            'message_id' => Str::uuid(),

                        ],
                    ],
                ],
            ]),
        ]);

        $messages[] = (new ClickSendMessage('testing message for success'))->to('+61422222222');
        $messages[] = (new ClickSendMessage('testing message for failure'))->to('+61433333333');

        $response = $this->smsc->sendManySms($messages);

        $this->assertFalse($response['success']);
        $this->assertEquals('testing message for failure', $response['failures']['FAILED'][0]->content);
        $this->assertEquals('+61433333333', $response['failures']['FAILED'][0]->to);
    }

    /** @test */
    public function it_returns_an_api_exception()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 500,
                'response_code' => 'EXCEPTION',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'FAILED',
                            'body' => 'testing message for failure',
                            'to' => '+61433333333',
                            'message_id' => Str::uuid(),

                        ],
                    ],
                ],
            ]),
        ]);

        $messages[] = (new ClickSendMessage('testing message for success'))->to('+61422222222');

        $response = $this->smsc->sendManySms($messages);

        $this->assertFalse($response['success']);
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

    /** @test */
    public function it_throws_exception_if_message_length_exceeds_determined_length()
    {
        $this->expectException(CouldNotSendNotification::class);

        $message = '';

        while (strlen($message) < 1001) {
            $message .= fake()->word();
        }

        $this->smsc->sendSms(
            'Test suite',
            '+61411111111',
            $message
        );
    }

    /** @test */
    public function it_throws_exception_if_message_is_not_string_or_clicksend_message_object()
    {
        $this->expectException(CouldNotSendNotification::class);

        $messages[] = new stdClass();

        $this->smsc->sendManySms(
            $messages
        );

    }

    public function it_can_call_the_results_callback_method()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 200,
                'response_code' => 'SUCCESS',
                'response_msg' => 'Here are you data.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'SUCCESS',
                            'message_id' => Str::uuid(),
                        ],
                    ],
                ],
            ]),
        ]);


        $notification = new TestNotificationWithResultCallback();
        $this->assertFalse($notification->resultsCallbackWasCalled);
        $response = $this->channel->send(
            new TestNotifiable(), $notification
        );
        $this->assertTrue($response['success']);
        $this->assertTrue($notification->resultsCallbackWasCalled);


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

class TestNotificationWithStringMessage extends \Illuminate\Notifications\Notification
{
    public function toClickSend()
    {
        return 'This is a message';
    }
}

class TestNotificationWithResultCallback extends \Illuminate\Notifications\Notification
{
    public function toClickSend()
    {
        return (new ClickSendMessage('messageContent'))->from('fromNumber');
    }

    public $resultsCallbackWasCalled = false;
    public function results($results) {
        $this->resultsCallbackWasCalled = true;
    }
}
