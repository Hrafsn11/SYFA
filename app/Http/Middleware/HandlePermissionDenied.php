<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class HandlePermissionDenied
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (AuthorizationException $e) {
            return $this->handlePermissionDenied($request, $e->getMessage());
        } catch (UnauthorizedException $e) {
            // Handle Spatie Permission exceptions
            return $this->handlePermissionDenied($request, 'You do not have the required permissions to access this resource.');
        }
    }

    /**
     * Handle permission denied errors
     *
     * @param \Illuminate\Http\Request $request
     * @param string $message
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function handlePermissionDenied(Request $request, string $message = null)
    {
        $defaultMessage = 'Access denied. You do not have permission to access this resource.';
        $errorMessage = $message ?? $defaultMessage;

        if ($request->expectsJson()) {
            // Return JSON response for API/AJAX requests
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => 'Forbidden',
                'code' => 403
            ], 403);
        }

        // For web requests
        if ($request->ajax()) {
            // Handle AJAX requests
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'redirect' => route('dashboard')
            ], 403);
        }

        // Check if we should show custom 403 page or redirect back
        if ($this->shouldShowErrorPage($request)) {
            return response()->view('errors.403', [
                'message' => $errorMessage,
                'title' => 'Access Denied'
            ], 403);
        }

        // Redirect back with error message
        return redirect()->back()->with([
            'error' => $errorMessage,
            'alert_type' => 'error'
        ]);
    }

    /**
     * Determine if we should show the error page
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function shouldShowErrorPage(Request $request): bool
    {
        // Show error page for direct resource access (not referrer from same domain)
        $referrer = $request->header('referer');
        $currentHost = $request->getHost();
        
        if (!$referrer) {
            return true;
        }

        $referrerHost = parse_url($referrer, PHP_URL_HOST);
        
        // If referrer is from different domain, show error page
        return $referrerHost !== $currentHost;
    }
}