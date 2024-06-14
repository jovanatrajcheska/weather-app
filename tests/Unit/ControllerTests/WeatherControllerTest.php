<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\WeatherController;
use App\Services\WeatherService;
use Illuminate\Validation\ValidationException;
use Mockery;
use Exception;

class WeatherControllerTest extends TestCase
{
    /** @var WeatherController */
    protected $controller;

    /** @var WeatherService|\Mockery\MockInterface */
    protected $weatherServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weatherServiceMock = Mockery::mock(WeatherService::class);
        $this->controller = new WeatherController($this->weatherServiceMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /** @test */
    public function it_returns_weather_view_with_valid_data()
    {
        $request = new Request(['city' => 'London']);

        // custom weather data
        $weatherData = [
            'temp' => 20,
            'humidity' => 80,
            'description' => 'Cloudy'
        ];
        $this->weatherServiceMock
            ->shouldReceive('getWeatherData')
            ->once()
            ->with('London')
            ->andReturn($weatherData);

        $response = $this->controller->getWeather($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('weather', $response->name());
        $this->assertArrayHasKey('weather', $response->getData());
        $this->assertEquals($weatherData, $response->getData()['weather']);
    }

    /** @test */
    public function city_is_missing()
    {
        $request = new Request();
        $this->expectException(ValidationException::class);
        $this->controller->getWeather($request);
    }

    /** @test */
    public function weather_service_exception_handling()
    {
        $request = new Request(['city' => 'Skopje']);

        $this->weatherServiceMock
            ->shouldReceive('getWeatherData')
            ->once()
            ->with('Skopje')
            ->andThrow(new Exception('Weather service unavailable'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Weather service unavailable');
        $this->controller->getWeather($request);
    }

    /** @test */
    public function city_field_is_empty()
    {
        $request = new Request(['city' => '']);
        $this->expectException(ValidationException::class);
        $this->controller->getWeather($request);
    }
}
