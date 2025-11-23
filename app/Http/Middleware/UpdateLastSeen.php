<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->user()) {
            DB::table('users')
                ->where('id', $request->user()->id)
                ->update(['last_seen' => now()]);
        }
        return $response;
    }
}
