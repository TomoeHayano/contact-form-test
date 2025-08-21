<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function create()
    {
        return view('auth.login');
    }

    public function attempt(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, remember: false)) {
            $request->session()->regenerate();
            return redirect('/admin');
        }

        return back()
            ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません'])
            ->withInput();
    }

    public function store(LoginRequest $request)
    {
        return $this->attempt($request);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
