<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

if (env('ENV') === 'local') {
    error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
    ini_set('display_errors', 'On');
} else {
    error_reporting(0);
    ini_set('display_errors', 'Off');
}

try {
    (new \App\Initialize())->action()->output();
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    (new \App\Slack())->sendErrorMessage($e->getMessage());
}
