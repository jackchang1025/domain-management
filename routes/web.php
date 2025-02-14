<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

Route::get('/', [RedirectController::class, 'redirectDomain']);
Route::get('/refresh-domains', [RedirectController::class, 'refreshDomainQueue'])
    ->middleware('auth'); // 建议添加认证中间件
