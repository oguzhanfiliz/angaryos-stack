<?php

//route içine taşı yada ortak bi alana
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//if(!@is_numeric(LARAVEL_START)) exit(1);

define('BASE_PATH', '/var/www/backend/');
$pipe['logRandom'] = date('dymHis').rand(100, 500);
$pipe['laravelStart'] = microtime(true);