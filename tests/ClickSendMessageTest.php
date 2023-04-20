<?php

namespace JordanHavard\ClickSend\Test;

use JordanHavard\ClickSend\ClickSendMessage;

class ClickSendMessageTest extends TestCase
{
    /** @test */
    public function it_can_accept_a_content_when_constructing_a_message()
    {
        $message = new ClickSendMessage('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_accept_a_content_when_creating_a_message()
    {
        $message = ClickSendMessage::create('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_content()
    {
        $message = (new ClickSendMessage())->content('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_from()
    {
        $message = (new ClickSendMessage())->from('John_Doe');

        $this->assertEquals('John_Doe', $message->from);
    }

    /** @test */
    public function it_can_set_the_to()
    {
        $message = (new ClickSendMessage())->to('1234567890');

        $this->assertEquals('1234567890', $message->to);
    }

    /** @test */
    public function it_can_set_a_custom_string()
    {
        $message = (new ClickSendMessage())->custom('123e4567-e89b-12d3-a456-426655440000');

        $this->assertEquals('123e4567-e89b-12d3-a456-426655440000', $message->custom);
    }

    /** @test */
    public function it_can_set_the_delay()
    {
        $message = (new ClickSendMessage())->delay('60');

        $this->assertEquals('60', $message->delay);
    }
}
