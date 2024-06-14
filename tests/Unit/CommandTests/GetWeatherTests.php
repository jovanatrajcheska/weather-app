<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use App\Console\Commands\GetWeather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Command;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Mockery;

class GetWeatherTest extends TestCase
{
    /**
     * @var WeatherService
     */

    protected function setUp(): void
    {
        parent::setUp();
    }
    public function get_weather_data_successfully()
    {
        Http::fake([
            '*' => Http::response([
                'main' => [
                    'temp' => 20,
                    'humidity' => 75
                ],
                'weather' => [
                    ['description' => 'Cloudy']
                ]
            ], 200)
        ]);

        Cache::shouldReceive('put')->once();
        Log::shouldReceive('info')->once();

        $command = new GetWeather(Mockery::mock(WeatherService::class));
        $command->setLaravel(app());

        $command->shouldReceive('argument')
            ->once()
            ->with('city')
            ->andReturn('London');

        $command->shouldReceive('info')->once()->with('Weather in London:');
        $command->shouldReceive('info')->once()->with('Temperature: 20');
        $command->shouldReceive('info')->once()->with('Humidity: 75');
        $command->shouldReceive('info')->once()->with('Description: Cloudy');

        $this->artisan('weather:get London')
            ->assertExitCode(0);
    }

    /** @test */
    public function http_error()
    {
        Http::fake([
            '*' => Http::response([], 404)
        ]);

        Log::shouldReceive('error')->once();

        $command = new GetWeather(Mockery::mock(WeatherService::class));
        $command->setLaravel(app());

        $command->shouldReceive('argument')
            ->once()
            ->with('city')
            ->andReturn('London');

        $command->shouldReceive('error')
            ->once()
            ->with('Failed to fetch weather data. Please check the city name and try again.');

        $this->artisan('weather:get London')
            ->assertExitCode(1);
    }

    /** @test */
    public function handles_exception_when_fetching_weather_data()
    {
        Http::fake([
            '*' => function () {
                throw new Exception('Weather service unavailable');
            }
        ]);

        Log::shouldReceive('error')->once();

        $command = new GetWeather(Mockery::mock(WeatherService::class));
        $command->setLaravel(app());

        $command->shouldReceive('argument')
            ->once()
            ->with('city')
            ->andReturn('London');

        $command->shouldReceive('error')
            ->once()
            ->with('An error occurred while fetching the weather data. Please try again later.');

        $this->artisan('weather:get London')
            ->assertExitCode(1);
    }
}
