<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Exception;

class WeatherServiceTest extends TestCase
{
    /**
     * @var WeatherService
     */
    protected $weatherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadEnvironmentVariables();
        $this->weatherService = new WeatherService(new Request(['api_key' => config('app.api_key')]));
    }

    protected function loadEnvironmentVariables()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(base_path());
        $dotenv->load();
    }

    /** @test */
    public function get_weather_data_successfully()
    {
        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={API key}' => Http::response([
                'main' => [
                    'temp' => 20,
                    'humidity' => 75
                ],
                'weather' => [
                    ['description' => 'Cloudy']
                ]
            ], 200)
        ]);

        $city = 'London';
        $weatherData = $this->weatherService->getWeatherData($city);

        $this->assertArrayHasKey('temp', $weatherData);
        $this->assertArrayHasKey('humidity', $weatherData);
        $this->assertArrayHasKey('description', $weatherData);
        $this->assertEquals(20, $weatherData['temp']);
        $this->assertEquals(75, $weatherData['humidity']);
        $this->assertEquals('Cloudy', $weatherData['description']);
    }

    /** @test */
    public function error_response()
    {
        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={API key}' => Http::response([], 404)
        ]);

        $city = 'NonExistingCity';
        $weatherData = $this->weatherService->getWeatherData($city);

        $this->assertArrayHasKey('error', $weatherData);
        $this->assertEquals('Error getting weather data. Check spelling or check if the city exists.', $weatherData['error']);
    }
}
