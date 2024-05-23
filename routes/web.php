<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here you can define all your application routes. The Router class
| provides a convenient way to register your routes and associate them
| with controller actions. Have fun.
|
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\DefaultController;
use App\Http\Controllers\AuthenticatorController;
use Framework\Support\Facades\Router;

Router::get('/', [DefaultController::class, 'default'])->name('default');
Router::get('/login', [AuthenticatorController::class, 'login'])->name('login');

Router::get('/auth', [AuthenticatorController::class, 'auth'])
    ->name('auth');

Router::get('/auth_redirect', [AuthenticatorController::class, 'auth_redirect'])
    ->name('auth_redirect');

Router::get('/qr_auth', [AuthenticatorController::class, 'qr_auth'])
    ->name('qr_auth');

Router::post('/qr_auth_code', [AuthenticatorController::class, 'qr_auth_code'])
    ->name('qr_auth_code');

Router::post('/auth_code', [AuthenticatorController::class, 'auth_code'])
    ->name('auth_code');

Router::get('/change_password', [AuthenticatorController::class, 'change_password'])
    ->name('change_password')
    ->pipes(['auth']);

Router::post('/change_password', [AuthenticatorController::class, 'change_password_submit'])->name('change_password_submit');

Router::get('/connect', [AuthenticatorController::class, 'connect'])
    ->name('connect');

Router::get('/google', [AuthenticatorController::class, 'google'])
    ->name('google');

Router::get('/google_logout', [AuthenticatorController::class, 'google_logout'])
    ->name('google_logout')
    ->pipes(['auth']);

Router::get('/home', [AuthenticatorController::class, 'home'])
    ->name('home')
    ->pipes(['auth']);

Router::post('/login_user', [AuthenticatorController::class, 'login_user'])
    ->name('loginUser');

Router::get('/logout', [AuthenticatorController::class, 'logout'])
    ->name('logout')
    ->pipes(['auth']);

Router::get('/number', [AuthenticatorController::class, 'number'])
    ->name('number');

Router::get('/register', [AuthenticatorController::class, 'register'])
    ->name('register');

Router::get('/register_progress', [AuthenticatorController::class, 'register_progress'])
    ->name('register_progress');

Router::get('/send_email', [AuthenticatorController::class, 'send_email'])
    ->name('send_email')
    ->pipes(['auth']);

Router::post('/sms', [AuthenticatorController::class, 'sms'])->name('sms');

Router::get('/send_qr_email', [AuthenticatorController::class, 'send_qr_email'])
    ->name('send_qr_email')
    ->pipes(['auth']);

Router::post('/number_validation', [AuthenticatorController::class, 'number_validation'])
    ->name('number_validation');

Router::get('/verify', [AuthenticatorController::class, 'showVerificationForm'])
    ->name('verify');

Router::get('/validate_code', [AuthenticatorController::class, 'validate_code'])
    ->name('validate_code');