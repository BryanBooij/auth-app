<?php

/*
|--------------------------------------------------------------------------
| Introduction
|--------------------------------------------------------------------------
|
| This application is made using our personal Framework. This Framework
| contains every tooling that makes a solid application abiding by PSR
| convention. Have fun.
|
|--------------------------------------------------------------------------
*/

use Framework\Http\Kernel;
use Framework\Component\Application;

require_once 'vendor/autoload.php';
require_once 'autoload.php';
require_once 'Framework/helpers.php';
require_once 'routes/web.php';
$app = require_once 'application.php';

$app->get(Kernel::class)->handle(request());