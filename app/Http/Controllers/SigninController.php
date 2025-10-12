<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigninController extends Controller
{
    public function index()
    {
        return view('layouts.sign-in', [
            'title' => 'sign-in',
            'active' => 'sign-in'
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->with('loginError', 'Boo, you failed');
    }

    public function logout(Request $request)
    {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
    }
}
