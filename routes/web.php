<?php

use App\Http\Controllers\CspReportController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::post('/csp-report', CspReportController::class)
    ->withoutMiddleware([
        PreventRequestForgery::class,
        StartSession::class,
        ShareErrorsFromSession::class,
    ])
    ->middleware('throttle:300,1')
    ->name('csp.report');
