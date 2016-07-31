<?php

/**
 * App bootstrap settings
 */

//composer autoload
require __DIR__ . "/../vendor/autoload.php";

//Exception handler
set_exception_handler(function ($exception) {
    /** @var Exception $exception */
    echo '[Exception] ' . $exception->getMessage() . PHP_EOL;
});
