<?php
//require 'autoload.php';
use Framework\Component\Application;


$app = new Application(getcwd());

$app->set_config_path(base_path('config'));

/*
|--------------------------------------------------------------------------
| Application Setup
|--------------------------------------------------------------------------
|
| Here you register your configurations and setting your application's
| services. These registrations will be used to bootstrap the
| application.
|
|--------------------------------------------------------------------------
*/
$included_files = get_included_files();
//var_dump($included_files);

$app->set_services([]);
$app->bootstrap();

return $app;
