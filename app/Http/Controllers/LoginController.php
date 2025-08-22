<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        $name = request()->cookie('user_name');
        return view('auth.login', compact('name'));
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            $user = User::where('email', $request->email)->where('status', 1)->first();

            if ($user === null) {
                throw new \Exception('The provided credentials do not match our records.');
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new \Exception('The provided credentials do not match our records.');
            }

            Cookie::queue('user_name', $user->name, 60 * 24 * 7);
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        request()->session()->invalidate();
        Auth::logout();
        return redirect('/login');
    }
}
