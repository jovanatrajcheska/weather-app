<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Middleware\ApiMiddleware;
use Closure;

class ApiMiddlewareTest extends TestCase
{
    /** @var ApiMiddleware */
    protected $middleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ApiMiddleware();
    }

    /** @test */
    public function api_key_test()
    {
        $request = new Request();
        $next = function ($request) {
            return $request;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertTrue($result->has('api_key'));
        $this->assertEquals(config('app.api-key'), $result->input('api_key'));
    }
}
