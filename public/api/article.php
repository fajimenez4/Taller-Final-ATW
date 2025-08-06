<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\ArticleController;

(new ArticleController())->handle();
