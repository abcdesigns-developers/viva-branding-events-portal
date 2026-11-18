<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Keeps Phase 2 modules switched off (404) until PORTAL_PHASE2=true. */
class EnsurePhase2Enabled
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(config('portal.phase2'), 404);

        return $next($request);
    }
}
