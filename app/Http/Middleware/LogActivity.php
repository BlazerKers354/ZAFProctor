<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     * Logs important user activities for audit purposes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users and specific routes
        if ($request->user() && $this->shouldLog($request)) {
            $this->logActivity($request);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged.
     */
    protected function shouldLog(Request $request): bool
    {
        // Log only POST, PUT, PATCH, DELETE requests
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Log the activity.
     */
    protected function logActivity(Request $request): void
    {
        $action = match ($request->method()) {
            'POST' => AuditLog::ACTION_CREATE,
            'PUT', 'PATCH' => AuditLog::ACTION_UPDATE,
            'DELETE' => AuditLog::ACTION_DELETE,
            default => 'unknown',
        };

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => $request->method() . ' ' . $request->path(),
        ]);
    }
}
