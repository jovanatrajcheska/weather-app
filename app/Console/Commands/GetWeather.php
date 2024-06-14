<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class GetWeather extends Command
{
    protected $signature = 'weather:get {city}';
    protected $description = 'Fetch weather information for a given city';
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $city = $this->argument('city');
        $apiKey = config('app.api-key');

        $url = 'https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={API key}';


        try {
            $response = Http::get($url, [
                'q' => $city,
                'appid' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $weatherData = [
                    'temperature' => $data['main']['temp'],
                    'humidity' => $data['main']['humidity'],
                    'description' => $data['weather'][0]['description']
                ];

                Cache::put("weather_{$city}", $weatherData, now()->addHour());

                $cityWeatherLog = "Weather data fetched successfully for {$city}: " . json_encode($weatherData);
                Log::info($cityWeatherLog);
                $this->info("Weather in {$city}:");
                $this->info("Temperature: {$weatherData['temperature']}"); // shown in Kelvin
                $this->info("Humidity: {$weatherData['humidity']}");
                $this->info("Description: {$weatherData['description']}");
            } else {
                $errorMessage = "Error fetching weather data for {$city}: " . $response->body();
                Log::error($errorMessage);
                $this->error("Failed to fetch weather data. Please check the city name and try again.");
            }
        } catch (Exception $e) {
            $exceptionMessage = "Exception fetching weather data for {$city}: " . $e->getMessage();
            Log::error($exceptionMessage);
            $this->error("An error occurred while fetching the weather data. Please try again later.");
        }
    }
}
