<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\BookController;

(new BookController())->handle();