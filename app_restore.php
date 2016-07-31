<?php

/**
 * Run this file on Dec20th, to fix the bug.
 * Ports data from vendor_special_day to vendor_schedule.
 */

require __DIR__ . '/config/bootstrap.php';

use Foodora\RestoreSchedule;

$restore = new RestoreSchedule();
$restore->process();
