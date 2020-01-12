<?php

declare(strict_types=1);

use Fugue\Controller\DefaultController;
use Fugue\HTTP\Routing\Route;

return [
    Route::get('/test', DefaultController::class . '@testPage', 'landing-page'),
];
