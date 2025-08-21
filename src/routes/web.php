<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ContactController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// お問い合わせフォーム
Route::get('/',         [ContactController::class, 'index']);
Route::post('/confirm', [ContactController::class, 'confirm']);
Route::post('/thanks',  [ContactController::class, 'store']);
Route::get('/thanks',   [ContactController::class, 'thanks']);

// 管理画面
Route::middleware('auth')->group(function () {
    Route::get('/admin',            [AdminController::class, 'index']);
    Route::get('/admin/export',     [AdminController::class, 'export']);
    Route::get('/admin/{contact}',  [AdminController::class, 'show']);
    Route::delete('/admin/{contact}', [AdminController::class, 'destroy']);
});

// 認証
Route::get('/auth', [AuthController::class, 'index']); // /authに変更
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');