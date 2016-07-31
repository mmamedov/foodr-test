<?php

/**
 * Run on Dec28th.
 * Restores vendor_schedule table to its original state
 */

require __DIR__ . '/config/bootstrap.php';

use Foodora\ImitateSpecialDays;

$imitate = new ImitateSpecialDays();
$imitate->process();
