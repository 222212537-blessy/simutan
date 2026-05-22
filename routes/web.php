<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Demo\DemoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pos\UserController;
use App\Http\Controllers\Pos\PermintaanController;
use App\Http\Controllers\Pos\KelompokController;
use App\Http\Controllers\Pos\BarangController;
use App\Http\Controllers\Pos\PilihanController;
use App\Http\Controllers\Pos\DefaultController;
use App\Http\Controllers\Pos\NotificationController;
use App\Http\Controllers\WizardController;
use App\Models\Permintaan;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Auth\LoginController;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/index', function () {
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();
    $totalPermintaanBulanIni = Permintaan::whereBetween('tgl_request', [$startOfMonth, $endOfMonth])->count();
    return view('index', ['totalPermintaanBulanIni' => $totalPermintaanBulanIni]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin/index', [DashboardController::class, 'index'])->name('admin.index');
    Route::get('/pegawai/index', [DashboardController::class, 'index'])->name('pegawai.index');

    Route::get('/pegawai/index', [UserController::class, 'topUsers'])->name('pegawai.index');

    Route::controller(NotificationController::class)->group(function () {
        Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
        Route::get('/notifications/view-all', 'viewAllNotifications')->name('notifications.viewAll');
        Route::get('/notifications/mark-as-read/{id}', 'markAsRead')->name('notifications.markAsRead');
        Route::get('/notifications/load', 'loadMore')->name('notifications.load');

    });

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/logout', 'destroy')->name('admin.logout');
        Route::get('/admin/profile', 'Profile')->name('admin.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');
        Route::get('admin/change/password', 'ChangePassword')->name('admin.change.password');
        Route::post('/update/password', 'UpdatePassword')->name('update.password');
    });

    Route::controller(PegawaiController::class)->group(function () {
        Route::get('/pegawai/logout', 'destroy')->name('pegawai.logout');
        Route::get('/pegawai/profile', 'Profile')->name('pegawai.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');
        Route::get('pegawai/change/password', 'ChangePassword')->name('pegawai.change.password');
        Route::post('/update/password', 'UpdatePassword')->name('update.password');
    });

    Route::controller(SupervisorController::class)->group(function () {
        Route::get('/supervisor/logout', 'destroy')->name('supervisor.logout');
        Route::get('/supervisor/profile', 'Profile')->name('supervisor.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');
        Route::get('/supervisor/change/password', 'ChangePassword')->name('supervisor.change.password');
        Route::post('/update/password', 'UpdatePassword')->name('update.password');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/all', 'UserAll')->name('user.all');
        Route::get('/user/add', 'UserAdd')->name('user.add');
        Route::post('/user/store', 'UserStore')->name('user.store');
        Route::get('/user/edit/{id}', 'UserEdit')->name('user.edit');
        Route::put('/user/update/{id}', 'UserUpdate')->name('user.update');
        Route::get('/user/delete/{id}', 'UserDelete')->name('user.delete');
    });

    Route::controller(PermintaanController::class)->group(function () {
        Route::get('/permintaan/all', 'PermintaanAll')->name('permintaan.all');
        Route::get('/permintaan/add', 'PermintaanAdd')->name('permintaan.add');
        Route::get('/permintaan/view/{id}', 'PermintaanView')->name('permintaan.view');
        Route::get('permintaan/approve/{id}', 'PermintaanApprove')->name('permintaan.approve');
        Route::patch('permintaan/update-status/{id}', 'PermintaanUpdateStatus')->name('permintaan.updateStatus');
        Route::get('/permintaan/saya', 'PermintaanSaya')->name('permintaan.saya');
        Route::get('/permintaan/{id}/edit', 'PermintaanEdit')->name('permintaan.edit');
        Route::put('/permintaan/{id}', 'PermintaanUpdate')->name('permintaan.update');
        Route::delete('/permintaan/delete/{id}', 'PermintaanDelete')->name('permintaan.delete');
        Route::get('/permintaan/print/{id}', 'PermintaanPrint')->name('permintaan.print');

        Route::get('permintaan/data', [PermintaanController::class, 'getPermintaanData'])->name('permintaan.data');
    });

    Route::controller(KelompokController::class)->group(function () {
        Route::get('/kelompok/all', 'KelompokAll')->name('kelompok.all');
        Route::get('/kelompok/add', 'KelompokAdd')->name('kelompok.add');
        Route::post('/kelompok/store', 'KelompokStore')->name('kelompok.store');
        Route::get('/kelompok/edit/{id}', 'KelompokEdit')->name('kelompok.edit');
        Route::put('/kelompok/update/{id}', 'KelompokUpdate')->name('kelompok.update');
        Route::get('/kelompok/delete/{id}', 'KelompokDelete')->name('kelompok.delete');
        Route::get('kelompok/data', 'data')->name('kelompok.data');

    });

    Route::controller(BarangController::class)->group(function () {
        Route::get('/barang/all', 'BarangAll')->name('barang.all');
        Route::get('/barang/all-act', 'BarangAllAct')->name('barang.allAct');
        Route::get('/barang/add', 'BarangAdd')->name('barang.add');
        Route::post('/barang/store', 'barangStore')->name('barang.store');
        Route::get('/barang/edit/{id}', 'BarangEdit')->name('barang.edit');
        Route::post('/barang/update/{id}', 'BarangUpdate')->name('barang.update');
        Route::delete('/barang/delete/{id}', 'BarangDelete')->name('barang.delete');
        Route::get('barang/data-all', 'dataForAll')->name('barang.data.all');
        Route::get('barang/data-index', 'dataForIndex')->name('barang.data.index');
        Route::post('/barang/add-stock', 'addStock')->name('barang.addStock');
        Route::get('/barang/export', 'exportToExcel')->name('barang.export');
        Route::get('/barang/pemasukan-export', 'exportPemasukan')->name('barang.pemasukan.export');
        Route::get('/barang/all', [BarangController::class, 'BarangAll'])
            ->middleware('save_awal_bulan')
            ->name('barang.all');
    });
        
    Route::controller(PilihanController::class)->group(function () {
        Route::get('/pilihan/all', 'PilihanAll')->name('pilihan.all');
        Route::get('/pilihan/add', 'PilihanAdd')->name('pilihan.add');
        Route::post('/pilihan/store', 'PilihanStore')->name('pilihan.store');
        Route::get('/pilihan/edit/{id}', 'PilihanEdit')->name('pilihan.edit');
        Route::put('/pilihan/update/{id}', 'PilihanUpdate')->name('pilihan.update');
        Route::get('/pilihan/admin-approval', 'PilihanAdminAppr')->name('pilihan.admAppr');
        Route::get('/pilihan/delete/{id}', 'PilihanDelete')->name('pilihan.delete');
        Route::get('/get-barang-list', [PilihanController::class, 'getBarangList'])->name('get-barang-list');
        Route::get('/pilihan/get-kategori/{kelompok_id}', 'getKategori')->name('pilihan.get-kategori');

    });

    Route::controller(DefaultController::class)->group(function () {
        Route::get('/get-category', 'GetCategory')->name('get-category');
        Route::get('/get-satuan', 'GetSatuan')->name('get-satuan');
    });
});

Route::controller(DemoController::class)->group(function () {
    Route::get('/about', 'Index')->name('about.page')->middleware('check');
    Route::get('/contact', 'ContactMethod')->name('contact.page');
});

Route::controller(WizardController::class)->group(function () {
    Route::get('/wizard', [WizardController::class, 'index'])->name('wizard.index');
    Route::post('/wizard-submit', [WizardController::class, 'submit'])->name('wizard.submit');
});

Route::get('/get-category', [App\Http\Controllers\Pos\PilihanController::class, 'getCategory'])->name('get-category');

require __DIR__.'/auth.php';
