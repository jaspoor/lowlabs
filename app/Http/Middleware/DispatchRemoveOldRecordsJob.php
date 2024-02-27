<?php

namespace App\Http\Middleware;

use App\Jobs\RemoveOldRecords;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DispatchRemoveOldRecordsJob
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        dispatch(new RemoveOldRecords());
        
        return $next($request);
    }
}
