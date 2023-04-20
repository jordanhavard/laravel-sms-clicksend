<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JordanHavard\ClickSend\ClickSendMessage;

class WhitelistingTest extends TestCase
{
    /** @test */
    public function it_checks_if_the_whitelist_is_disabled_by_default()
    {
        $this->assertFalse(Config::get('clicksend.whitelist_enabled'));
    }

    /** @test */
    public function it_throws_exception_if_whitelist_is_enabled_without_whitelist_set()
    {
        Config::set('clicksend.whitelist_enabled', true);
        $this->expectExceptionMessage('No whitelist found for current environment');

        $this->sendSms();
    }

    /** @test */
    public function it_can_send_to_a_number_on_the_whitelist()
    {
        Config::set('clicksend.whitelist_enabled', true);
        Config::set('clicksend.whitelists.testing', '+61422222222');
        $result = $this->sendSms('Testing', '+61422222222');

        $this->assertEquals('+61422222222', $result['data']['to']);
    }

    /** @test */
    public function it_replaces_a_number_not_on_the_whitelist_when_active()
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => 200,
                'response_code' => 'SUCCESS',
                'response_msg' => 'Messages queued for delivery.',
                'data' => (object) [
                    'messages' => [
                        [
                            'status' => 'SUCCESS',
                            'to' => '+61422222222',
                            'message_id' => Str::uuid(),
                        ],
                    ],
                ],
            ]),
        ]);

        Config::set('clicksend.whitelist_enabled', true);
        Config::set('clicksend.whitelists.testing', '+61422222222');
        $result = $this->sendSms('Testing', '+61433333333');

        $this->assertStringContainsString('WHITELIST_PREVENTED:', $result['data']['api_response']);
        $this->assertEquals('+61433333333', $result['data']['to']);
    }

    /** @test */
    public function it_returns_successful_responses_with_whitelist_enabled()
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
                            'to' => '+61411111111',
                            'message_id' => Str::uuid(),
                        ],
                        [
                            'status' => 'SUCCESS',
                            'to' => '+61433333333',
                            'message_id' => Str::uuid(),

                        ],
                    ],
                ],
            ]),
        ]);

        Config::set('clicksend.whitelist_enabled', true);
        Config::set('clicksend.whitelists.testing', '+61411111111');

        $messages[] = (new ClickSendMessage('Test'))->to('+61411111111');
        $messages[] = (new ClickSendMessage('test'))->to('+61422222222');

        $response = $this->smsc->sendManySms($messages);

        $this->assertTrue($response['success']);
        $this->assertStringContainsString('2 of 2', $response['message']);
        $this->assertStringContainsString('WHITELIST_ALLOWED:', $response['data']['messages'][0]['api_response']);
        $this->assertStringContainsString('WHITELIST_PREVENTED:', $response['data']['messages'][1]['api_response']);
    }
}
