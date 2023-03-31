<?php

namespace NotificationChannels\ClickSend;

class ClickSendMessage
{
    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from = '';

    /**
     * The phone number the message should be sent to.
     *
     * @var string
     */
    public $to = '';

    /**
     * The message content.
     *
     * @var string
     */
    public $content = '';

    /**
     * Timestamp delay
     *
     * @var int
     */
    public $delay = null;

    /**
     * Custom string on the message
     *
     * @var string
     */
    public $custom = null;

    /**
     * Create a new message instance.
     *
     * @param  string $content
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * @param  string  $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the a custom string on the message
     *
     * @param  string  $custom
     * @return $this
     */
    public function custom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Set the phone number or sender name the message should be sent from.
     *
     * @param  string  $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the phone number the message should be sent to.
     *
     * @param  string  $to
     * @return $this
     */
    public function to($to)
    {
        $this->to = $to;
        
        return $this;
    }

    /**
     * Set the delay when this should be sent
     *
     * Leave blank for immediate delivery.
     *
     * @param  string  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }
}
