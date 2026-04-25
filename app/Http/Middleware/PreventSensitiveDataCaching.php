<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSensitiveDataCaching
{
    /**
     * Prevent browser and proxy caching for authenticated responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$request->user()) {
            return $response;
        }

        $cacheControl = strtolower((string) $response->headers->get('Cache-Control', ''));
        if (str_contains($cacheControl, 'no-store')) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');

        return $response;
    }
}
