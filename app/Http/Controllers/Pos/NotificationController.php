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
        $barangs = Barang::all();
        $usersToNotify = User::whereIn('role', ['admin'])->get();

        $predictionYear = now()->year;
        $referenceYear = $predictionYear - 1;

        foreach ($barangs as $barang) {
            $averageQuarterlyUsage = DB::table('pengeluarans')
                ->where('barang_id', $barang->id)
                ->whereYear('tanggal', $referenceYear)
                ->selectRaw('QUARTER(tanggal) as quarter, SUM(qty) as total_qty')
                ->groupBy(DB::raw('QUARTER(tanggal)'))
                ->get()
                ->avg('total_qty');

            $quarterCount = DB::table('pengeluarans')
                ->where('barang_id', $barang->id)
                ->whereYear('tanggal', $referenceYear)
                ->selectRaw('COUNT(DISTINCT QUARTER(tanggal)) as quarter_count')
                ->value('quarter_count');

            $stockNotificationPrefix = "Stok barang {$barang->nama} diprediksi akan habis";

            if ($averageQuarterlyUsage > 0 && $quarterCount >= 4 && $barang->qty_item > 0) {
                $avgRounded = round($averageQuarterlyUsage);

                if ($averageQuarterlyUsage >= 5) {
                    $category = 'sering dipakai';
                    $shouldNotify = $barang->qty_item < 5;
                    $thresholdText = 'Batas kritis: < 5 unit';
                } else {
                    $category = 'jarang dipakai';
                    $shouldNotify = $barang->qty_item <= $averageQuarterlyUsage;
                    $thresholdText = "Batas kritis: <= {$avgRounded} unit";
                }

                if ($shouldNotify) {
                    $message = "Stok barang {$barang->nama} diprediksi akan habis (Sisa stok: {$barang->qty_item}, Rata-rata pengeluaran 4 kuartal tahun lalu: {$avgRounded}, Kategori: {$category}, {$thresholdText}). Perlu penambahan atau pengadaan stok barang.";

                    Notification::whereIn('user_id', $usersToNotify->pluck('id'))
                        ->where('message', 'like', $stockNotificationPrefix . '%')
                        ->where('message', '!=', $message)
                        ->where('is_read', false)
                        ->delete();

                    foreach ($usersToNotify as $user) {
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
                } else {
                    Notification::whereIn('user_id', $usersToNotify->pluck('id'))
                        ->where('message', 'like', $stockNotificationPrefix . '%')
                        ->where('is_read', false)
                        ->delete();
                }
            } else {
                Notification::whereIn('user_id', $usersToNotify->pluck('id'))
                    ->where('message', 'like', $stockNotificationPrefix . '%')
                    ->where('is_read', false)
                    ->delete();
            }
        }
    }
    public function markAllRead()
    {
        $user = Auth::user();
        $query = Notification::where('user_id', $user->id)
                    ->where('is_read', false);
        $query->update(['is_read' => true]);
    
        return response()->json(['status' => 'success']);
    }

    public function viewAllNotifications()
    {
        $user = Auth::user();
        $query = Notification::where('user_id', $user->id);
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
