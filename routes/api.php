<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/auth.php';
    // require __DIR__ . '/user.php';
    // require __DIR__ . '/channels.php';
    // require __DIR__ . '/messages.php';
});