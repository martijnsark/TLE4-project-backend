<?php

use Illuminate\Support\Facades\Route;

// Serve the Impakt web frontend (public/index.html) at "/".
// nginx routes "/" to index.php (this controller), so we hand back the SPA's
// index.html here. Static assets (_expo/, assets/) are served directly by the
// webserver, and /api keeps hitting the API routes. Falls back to the version
// payload until the frontend build is uploaded. See README § Deployment.
Route::get('/', function () {
    $index = public_path('index.html');

    return file_exists($index)
        ? response()->file($index)
        : ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';
