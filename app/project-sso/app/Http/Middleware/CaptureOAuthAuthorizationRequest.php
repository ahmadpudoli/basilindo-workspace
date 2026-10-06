<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureOAuthAuthorizationRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('oauth/authorize') && $request->isMethod('GET') && ! auth()->check()) {
            $request->session()->put('oauth_authorize_url', $request->fullUrl());
        }

        return $next($request);
    }
}
