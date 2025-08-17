<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;

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
        // バリデーションはここに来る前に完了
        // → 信頼できる入力だけ取り出すなら validated() を使う
        $credentials = $request->validated();

        if (Auth::attempt($credentials, remember: false)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        return back()
            ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません'])
            ->withInput();
    }
}
