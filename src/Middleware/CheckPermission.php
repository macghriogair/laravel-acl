<?php

namespace Macgriog\Acl\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param array $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions = [])
    {
        if (! Auth::guard()->guest()
            && $request->user()->hasAccess($permissions)) {
            return $next($request);
        }
        if ($request->wantsJson()) {
            return response()->json(['error' =>'Unauthorized.'], 403);
        }
        abort(403, 'Sorry, you are not authorized.');
    }
}
