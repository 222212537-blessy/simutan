<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AddNotificationsToView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek stok barang secara berkala (setiap 1 jam) jika user adalah admin/supervisor
            if (in_array($user->role, ['admin', 'supervisor'])) {
                \Illuminate\Support\Facades\Cache::remember('stock_notification_checked', 3600, function () {
                    app(\App\Http\Controllers\Pos\NotificationController::class)->checkStockAndNotify();
                    return true;
                });
            }

            $unreadCount = Notification::where('user_id', $user->id)
                                    ->where('is_read', false)
                                    ->count();
            view()->share('unreadCount', $unreadCount);
        }

        return $next($request);
    }
}
