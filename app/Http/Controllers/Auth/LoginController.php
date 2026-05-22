<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\SaveAwalBulan;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed
            return redirect()->intended('dashboard');
        }

        // Authentication failed
        return redirect()->back()->withErrors([
            'email' => 'Masuk gagal, silakan coba lagi.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('admin')) {
            // Jalankan middleware SaveAwalBulan setelah admin login
            app(SaveAwalBulan::class)->handle($request, function () {});
    
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('supervisor')) {
            // Tambahkan redirect untuk role supervisor
            return redirect()->route('supervisor.dashboard');
        } elseif ($user->hasRole('pegawai')) {
            return redirect()->route('pegawai.dashboard');
        }
    
        return redirect('/');
    }
    

}
