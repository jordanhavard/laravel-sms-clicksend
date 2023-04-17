<?php

namespace JordanHavard\ClickSend\Test;

use Mockery;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
