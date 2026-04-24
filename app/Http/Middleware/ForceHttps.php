<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     * Redirect HTTP to HTTPS in production environment.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $forwardedProto = strtolower((string) $request->headers->get('X-Forwarded-Proto', ''));
        $forwardedHeader = strtolower((string) $request->headers->get('Forwarded', ''));

        $isForwardedSecure = str_contains($forwardedProto, 'https')
            || str_contains($forwardedHeader, 'proto=https');

        if (app()->environment('production') && !$request->secure() && !$isForwardedSecure) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
