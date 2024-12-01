<?php

namespace App\Http\Middleware;

use App\Http\Traits\SetupHelper;
use Closure;
use Illuminate\Http\Request;

class CheckSetupIsDone
{
    use SetupHelper;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $checkSetup = $this->checkSetupIsDone();

        if (!$checkSetup) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
