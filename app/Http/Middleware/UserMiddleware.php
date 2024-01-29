<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;


class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /*public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }*/

    // app/Http/Middleware/UserMiddleware.php

    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role_id == 2) {
            // Store the userId in the session
            session(['userId' => auth()->user()->id]);

            return $next($request);
        }

        abort(403, 'Unauthorized');
    }

}
