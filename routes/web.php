<?php

use App\Controllers\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/articles/{id:\d+}', [HomeController::class, 'show']],
    ['POST', '/articles', [HomeController::class, 'store']],
];