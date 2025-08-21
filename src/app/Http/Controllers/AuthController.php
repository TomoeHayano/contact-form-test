<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {   
        $categories = [];
        return view('index', ['categories' => $categories]);
    }

    // ユーザー登録処理のメソッド
    public function store(Request $request)
    {
        // 基本的なバリデーション
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        
        // ユーザー作成
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        // 自動ログインせず、ログイン画面へリダイレクト
        return redirect('/login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }
}