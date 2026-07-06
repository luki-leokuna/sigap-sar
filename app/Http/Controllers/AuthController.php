<?php

namespace App\Http\Controllers;

use App\Models\MemberLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        $user = Auth::user();
        if ($user && $user->isMember()) {
            $user->update(['status' => 'online']);
            MemberLocation::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['is_online' => true, 'last_seen_at' => now()]
            );
        }

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'member.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda sudah keluar.');
    }
}
