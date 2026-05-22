<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permintaan;
use App\Models\Notification;
use App\Models\Barang;

use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();

    // Inisialisasi variabel $query dengan nilai default
    $query = Permintaan::query();

    if ($user->role === 'admin') {
        $query->whereIn('status', ['pending'])
              ->orderBy('created_at', 'desc')
              ->limit(5);
    } elseif ($user->role === 'supervisor') {
        $query->whereIn('status', ['pending', 'approved by admin'])
              ->orderBy('created_at', 'desc')
              ->limit(5);
    }

    // Ambil data lainnya
    $barangs = Barang::all();
    $userschart = User::all();
    $kelompoks = Kelompok::with('barangs')->get();

    $notifications = Notification::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    $unreadCount = Notification::where('user_id', $user->id)
        ->where('is_read', false)
        ->count();

    $kelompokWithMostBarangs = $kelompoks->sortByDesc(function ($kelompok) {
        return $kelompok->barangs->count();
    })->first();

    // Mengambil top 5 barang berdasarkan total request quantity
    $topBarangs = DB::table('barangs')
    ->join('pilihans', 'barangs.id', '=', 'pilihans.barang_id')
    ->select('barangs.nama', DB::raw('SUM(pilihans.req_qty) as total_qty'))
    ->groupBy('barangs.nama')
    ->orderBy('total_qty', 'desc')
    ->limit(5)
    ->get();

    $topUsers = User::select('users.id', 'users.panggilan', 'users.foto', DB::raw('COUNT(permintaans.id) as requests'))
        ->leftJoin('permintaans', 'users.id', '=', 'permintaans.user_id')
        ->groupBy('users.id', 'users.panggilan', 'users.foto')
        ->orderBy('requests', 'desc')
        ->limit(3) // Batasi jumlah pengguna yang ditampilkan, misalnya 5 pengguna teratas
        ->get();

        if ($user->role === 'admin') {
            $permintaans = $query->get();
            return view('admin.index', compact('user','permintaans', 'barangs', 'kelompoks', 'kelompokWithMostBarangs', 'topBarangs', 'notifications', 'unreadCount', 'topUsers'));
        } elseif ($user->role === 'pegawai') {
            $permintaans = $query->where('user_id', $user->id)->get();
            return view('pegawai.index', compact('user','permintaans', 'barangs', 'kelompoks', 'notifications', 'unreadCount', 'kelompokWithMostBarangs', 'topBarangs', 'topUsers'));
        } elseif ($user->role === 'supervisor') {
            $permintaans = $query->where('user_id', $user->id)->get();
            return view('supervisor.index', compact('user','permintaans', 'barangs', 'kelompoks', 'notifications', 'unreadCount', 'kelompokWithMostBarangs', 'topBarangs', 'topUsers'));
        }
}
   



    public function dashboard()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
        $unreadCount = Notification::where('user_id', $user->id)
                                ->where('is_read', false)
                                ->count();

        return view('dashboard', compact('notifications', 'unreadCount'));
    }

    public function PermintaanAll()
    {
        $permintaans = Permintaan::latest()->get();
        return view('backend.permintaan.permintaan_all', compact('permintaans'));
    }

    public function BarangAll()
    {
        $barangs = Barang::latest()->get();
        return view('backend.barang.barang_all', compact('barangs'));
    }
}
