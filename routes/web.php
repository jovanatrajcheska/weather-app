<?php

use Illuminate\Support\Facades\Route;

use App\Services\WeatherService;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/weather');
});

Route::get('/weather', function () {
    return view('weather');
});

Route::post('/weather', [WeatherController::class, 'getWeather']);
