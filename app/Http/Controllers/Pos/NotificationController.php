<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        $user = Auth::user();
        $query = Notification::where('user_id', $user->id)
                    ->where('is_read', false);

        // // Cek role user dan tambahkan filter yang sesuai
        // if ($user->role == 'supervisor') {
        //     $query->whereHas('permintaan', function ($q) {
        //         $q->where('status', 'approved by admin');
        //     });
        // }

        $query->update(['is_read' => true]);
    
        return response()->json(['status' => 'success']);
    }

    public function viewAllNotifications()
    {
        $user = Auth::user();
        $query = Notification::where('user_id', $user->id);

        // // Cek role user dan tambahkan filter yang sesuai
        // if ($user->role == 'supervisor') {
        //     $query->whereHas('permintaan', function ($q) {
        //         $q->where('status', 'approved by admin');
        //     });
        // }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        return view('backend.notification.notification_view', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return redirect()->route('notifications.viewAll')->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    public function loadMore(Request $request)
    {
        $user = Auth::user();
        $offset = $request->input('offset', 0);
        $limit = 5;

        $query = Notification::where('user_id', $user->id);

        // // Cek role user dan tambahkan filter yang sesuai
        // if ($user->role == 'supervisor') {
        //     $query->whereHas('permintaan', function ($q) {
        //         $q->where('status', 'approved by admin');
        //     });
        // }

        $notifications = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json($notifications);
    }

    public function showHeaderNotifications()
    {
        $user = Auth::user();
        $query = Notification::where('user_id', $user->id);

        // // Filter khusus untuk supervisor
        // if ($user->role == 'supervisor') {
        //     $query->whereHas('permintaans', function ($q) {
        //         $q->where('status', 'approved by admin');
        //     });
        // }

        $notifications = $query->orderBy('created_at', 'desc')->limit(5)->get();
        $unreadCount = $notifications->where('is_read', false)->count();

        // Simpan data ke session untuk digunakan di view
        session([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);

        return view('your-header-view'); // Ganti dengan view header Anda
    }

}
