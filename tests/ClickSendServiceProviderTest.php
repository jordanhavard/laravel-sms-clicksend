<?php

namespace JordanHavard\ClickSend\Test;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use JordanHavard\ClickSend\ClickSendApi;
use JordanHavard\ClickSend\ClickSendServiceProvider;
use Mockery\MockInterface;

class ClickSendServiceProviderTest extends TestCase
{
    /** @var Application|MockInterface */
    private $appMock;

    /** @var Repository|MockInterface */
    private $configMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->appMock = $this->mock(Application::class);
        $this->configMock = $this->mock(Repository::class);
    }

    /** @test */
    public function it_registers_the_clicksend_api_singleton()
    {
        $app = $this->createApplication();

        Config::set('services.clicksend', [
            'username' => 'foo',
            'api_key' => 'bar',
        ]);

        // Register the service provider
        $app->register(ClickSendServiceProvider::class);

        // Get an instance of the ClickSendApi from the container
        $clickSendApi = $app->make(ClickSendApi::class);

        // Check that the instance is a singleton
        $this->assertInstanceOf(ClickSendApi::class, $clickSendApi);
        $this->assertSame($clickSendApi, $app->make(ClickSendApi::class));
    }

    /** @test */
    public function it_provides_the_clicksend_api_singleton()
    {
        $serviceProvider = new ClickSendServiceProvider($this->appMock);
        $this->assertEquals([ClickSendApi::class], $serviceProvider->provides());
    }
}
