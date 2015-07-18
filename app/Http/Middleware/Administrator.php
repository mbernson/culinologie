<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Administrator extends Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return $this->deny($request);
        }

        if (! Auth::user()->isApproved()) {
            return redirect()->back()->with('warning', 'Je account moet goedgekeurd zijn om dat te kunnen doen.');
        }

        if (! Auth::user()->isAdmin()) {
            return redirect()->back()->with('warning', 'Je moet een beheerder zijn om dat te kunnen doen.');
        }

        return $next($request);
    }

}
