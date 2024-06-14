<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeather(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'units' => 'nullable|in:metric,imperial'
        ]);

        $city = $request->input('city');
        $units = $request->input('units', '');

        $weather = $this->weatherService->getWeatherData($city, $units);

        return view('weather', compact('weather'));
    }
}
