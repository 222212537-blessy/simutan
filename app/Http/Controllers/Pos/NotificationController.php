<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function checkStockAndNotify()
    {
        // Ambil semua barang
        $barangs = Barang::all();
        // Ambil admin/supervisor yang akan menerima notifikasi
        $usersToNotify = User::whereIn('role', ['admin', 'supervisor'])->get();

        foreach ($barangs as $barang) {
            // Hitung rata-rata jumlah pengeluaran barang dalam satu kuartal
            $averageQuarterlyUsage = DB::table('pengeluarans')
                ->selectRaw('YEAR(tanggal) as year, QUARTER(tanggal) as quarter, SUM(qty) as total_qty')
                ->where('barang_id', $barang->id)
                ->groupBy('year', 'quarter')
                ->get()
                ->avg('total_qty');

            // Jika stok barang <= rata-rata satu kuartal per barangnya (dan stok belum habis / > 0)
            if ($averageQuarterlyUsage > 0 && $barang->qty_item <= $averageQuarterlyUsage && $barang->qty_item > 0) {
                $avgRounded = round($averageQuarterlyUsage);
                $message = "Stok barang {$barang->nama} diprediksi akan habis (Sisa stok: {$barang->qty_item}, Rata-rata pengeluaran 1 kuartal: {$avgRounded}). Perlu penambahan atau pengadaan stok barang.";
                
                foreach ($usersToNotify as $user) {
                    // Cek agar tidak mengirimkan notifikasi ganda yang belum dibaca
                    $existingNotification = Notification::where('user_id', $user->id)
                        ->where('message', $message)
                        ->where('is_read', false)
                        ->exists();

                    if (!$existingNotification) {
                        Notification::create([
                            'user_id' => $user->id,
                            'permintaan_id' => null,
                            'message' => $message,
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }
    }
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
