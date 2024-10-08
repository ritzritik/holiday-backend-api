<?php
// app/Http/Middleware/VerifyAjaxCsrfToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyAjaxCsrfToken extends Middleware
{
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            $token = $request->header('X-CSRF-TOKEN');
            if (!$token) {
                return response()->json(['error' => 'CSRF token missing'], 422);
            }
            if (!$this->isTokenValid($token)) {
                return response()->json(['error' => 'Invalid CSRF token'], 422);
            }
        }
        return $next($request);
    }

    protected function isTokenValid($token)
    {
        // Verify the CSRF token using the VerifyCsrfToken middleware
        $verifyCsrfToken = new VerifyCsrfToken();
        return $verifyCsrfToken->isTokenValid($token);
    }
}
