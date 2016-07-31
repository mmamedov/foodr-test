<?php

/**
 * Auto increment value of vendor_schedule table before the fix
 *
 * Based on initial test data given(7 records in vendor_schedule), value of 8 has been set.
 * If your dataset is different, this number needs to be changed accordingly.
 *
 * !IMPORTANT: Set this value before executing fix.
 * Auto_increment value for vendor_schedule table
 * To get current auto_increment value from vendor_schedule, execute this query and instead of 0 write the result:
 *
    SELECT auto_increment FROM information_schema.tables
    WHERE table_schema='foodora-test' AND table_name='vendor_schedule'
 */

return 8;
