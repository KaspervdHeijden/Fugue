<?php

declare(strict_types=1);

use Fugue\Controller\DefaultController;
use Fugue\HTTP\Routing\Route;

return [
    Route::get('/', DefaultController::class . '@onLoad', 'landing-page'),
];
