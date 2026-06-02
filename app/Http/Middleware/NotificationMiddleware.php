<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Permintaan;

class NotificationMiddleware
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
        if (Auth::check()) {
            $user = Auth::user();

            // Jalankan pengecekan stok barang sekali setiap 1 jam untuk admin/supervisor
            if (in_array($user->role, ['admin', 'supervisor'])) {
                \Illuminate\Support\Facades\Cache::remember('stock_notification_checked', 3600, function () {
                    app(\App\Http\Controllers\Pos\NotificationController::class)->checkStockAndNotify();
                    return true;
                });
            }

            $notifications = Notification::where('user_id', $user->id)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
            $unreadCount = Notification::where('user_id', $user->id)
                                        ->where('is_read', false)
                                        ->count();

            // Menyimpan data ke dalam session untuk digunakan di view
            $request->session()->put('notifications', $notifications);
            $request->session()->put('unreadCount', $unreadCount);
        }

        return $next($request);
    }
}
