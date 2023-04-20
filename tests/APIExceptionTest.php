<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Support\Facades\Http;

class APIExceptionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider Requests
     */
    public function it_handles_api_exceptions_correctly($code, $response)
    {
        Http::fake([
            '*' => Http::response([
                'http_code' => $code,
                'response_code' => $response,
            ]),
        ]);

        $res = $this->sendSms();

        $this->assertEquals($response, $res['message']);
    }

    public function Requests()
    {
        return [
            [400, 'BAD_REQUEST'],
            [401, 'UNAUTHORISED'],
            [403, 'FORBIDDEN'],
            [404, 'NOT_FOUND'],
            [405, 'BAD_METHOD'],
            [429, 'TOO_MANY_REQUESTS'],
            [500, 'INTERNAL_SERVER_ERROR'],
            [100, 'RESPONSE_NOT_OK'],
            [300, 'RESPONSE_NOT_OK'],
        ];
    }
}
