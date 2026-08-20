<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SyncUserStats
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->last_activity_date) {
                $lastActivity = Carbon::parse($user->last_activity_date)->startOfDay();
                $yesterday = Carbon::yesterday()->startOfDay();

                // Jika aktivitas terakhir lebih lama dari kemarin (berarti kemarin bolong)
                if ($lastActivity->isBefore($yesterday) && $user->current_streak > 0) {
                    $user->current_streak = 0;
                    $user->save();
                }
            }
        }

        return $next($request);
    }
}
