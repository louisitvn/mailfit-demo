<?php

namespace Acelle\Http\Middleware;

use Closure;

class Backend
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //// Site offline
        //if (\Acelle\Model\Setting::get('site_online') == 'false' &&
        //    (null !== $request->user() && $request->user()->getOption('backend', 'setting_access_when_offline') != 'yes')
        //) {
        //    return redirect()->action('Controller@offline');
        //}

        // check if user not authorized for backend access
        if (null !== $request->user() && !$request->user()->userGroup->backend_access) {
            return redirect()->action('Controller@notAuthorized');
        }

        return $next($request);
    }
}
