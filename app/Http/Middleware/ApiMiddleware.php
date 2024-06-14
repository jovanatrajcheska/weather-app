<?php
namespace App\Http\Middleware;
use Exception;
use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
{
  public function handle(Request $request, Closure $next)
    {
        
        $request->merge(['api_key' => config('app.api-key')]);

        return $next($request);
    }
}