<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated(), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->route(
            $request->user()->role === 'admin' ? 'admin.products.index' : 'catalog.index'
        );
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
