<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Exception;

class WeatherService
{
    protected $apiKey;

    public function __construct(Request $request)
    {
        $this->apiKey = $request->get('api_key');
    }

    public function getWeatherData($city, $units = '')
    {
        $url = 'https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={API key}';

        try {
            $response = Http::get($url, [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => $units ?: 'standard' // Default to Kelvin if units not specified
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'temp' => $data['main']['temp'],
                    'humidity' => $data['main']['humidity'],
                    'description' => $data['weather'][0]['description']
                ];
            } else {
                return [
                    'error' => 'Error getting weather data. Check spelling or check if the city exists.'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
