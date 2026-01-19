<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::middleware('auth:api')->group(function () {
        Route::prefix('workspace')->group(function () {
            Route::get("/", [App\Http\Controllers\V1\Workspace\WorkspaceController::class, 'getWorkspaces']);
            Route::post("/create", [App\Http\Controllers\V1\Workspace\WorkspaceController::class, 'createWorkspace']);
            Route::post("/invite-members", [App\Http\Controllers\V1\Workspace\WorkspaceController::class, 'inviteMembers']);
            Route::post("/join", [App\Http\Controllers\V1\Workspace\WorkspaceController::class, 'joinWorkspace']);
        });
    });


    require __DIR__ . '/auth.php';
});
