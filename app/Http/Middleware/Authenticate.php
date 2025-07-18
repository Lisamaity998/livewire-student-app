<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request)
    {
        // return $request->expectsJson() ? null : route('login');
        if (! $request->expectsJson()) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('student/*')) {
                return route('student.login');
            }
    
            // return route('login');
            abort(403, 'Unauthorized');
        }
    }
}
