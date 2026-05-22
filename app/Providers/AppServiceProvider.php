<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Permintaan;

/**
 * Bootstrap any application services.
 *
 * @return void
 */

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('id');
        
        View::composer('*', function ($view) {
            // Mendapatkan tanggal awal dan akhir bulan ini
            $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        
            // Mendapatkan pengguna yang sedang login
            $user = Auth::user();
        
            // Inisialisasi variabel untuk jumlah permintaan
            $totalPermintaanBulanIni = 0;
            $totalPermintaanSelesai = 0;
            $totalPermintaanPending = 0;
            $totalPermintaanRejected = 0;
        
            if ($user) {
                if ($user->role === 'admin' || 'supervisor') {
                    // Admin melihat semua permintaan
                    $totalPermintaanBulanIni = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])->count();
                    $totalPermintaanSelesai = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->where('status', 'approved by supervisor')
                        ->count();
                    $totalPermintaanPending = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['pending', 'approved by admin'])
                        ->count();
                    $totalPermintaanRejected = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['rejected by admin', 'rejected by supervisor'])
                        ->count();
                } elseif ($user->role === 'pegawai') {
                    // Pegawai melihat hanya permintaan mereka sendiri
                    $totalPermintaanBulanIni = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->where('user_id', $user->id)
                        ->count();
                    $totalPermintaanSelesai = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->where('user_id', $user->id)
                        ->where('status', 'approved by supervisor')
                        ->count();
                    $totalPermintaanPending = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->where('user_id', $user->id)
                        ->whereIn('status', ['pending', 'approved by admin'])
                        ->count();
                    $totalPermintaanRejected = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])
                        ->where('user_id', $user->id)
                        ->whereIn('status', ['rejected by admin', 'rejected by supervisor'])
                        ->count();
                }
            }
        
            // Menyuntikkan data ke view
            $view->with([
                'totalPermintaanBulanIni' => $totalPermintaanBulanIni,
                'totalPermintaanSelesai' => $totalPermintaanSelesai,
                'totalPermintaanPending' => $totalPermintaanPending,
                'totalPermintaanRejected' => $totalPermintaanRejected,
            ]);
        });
    }
}
