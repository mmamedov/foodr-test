## 1. app_fix.php

This is where we fix the problem. Logic is inside ImitateSpecialDays class 

### Problem analysis:
- Edit schedule for vendors who has entry in `vendor_special_day`. 
- `vendor_schedule` table doesn't have a closed/opened field by design. 
Instead, if a vendor doesn't have entry for a weekday, it is assumed closed on that day. 
That's why we will not add "closed" entries from `vendor_special_day`.
- Dec 21-27 fall exactly on 1 week from Mon to Sun. It's safe to insert entries from `vendor_special_day` into 
`vendor_schedule` 
(otherwise it wouldn't be safe, what if there were two entries for 2 different Mondays in special_day table?)
- First take backup of conflicting schedules into a backup table `vendor_schedule_backup`, 
then delete these entries from `vendor_schedule`. After that safely transfer all data from `vendor_special_day` 
into `vendor_schedule` table, omitting "closed" entries. 
- A conflicting entry in `vendor_schedule` table is an entry(or entries) that has the same weekday as the entry
in `vendor_special_day` for the same vendor. If entry exists for a weekday in regular schedule table that is not 
in special days table for the same vendor, we do not touch such an entry.

### Execution
1. Composer install autoloader `composer update -o`
2. Edit config/db_pdo.php file with correct database credentials
3. Edit config/auto_increment.php file, see file for details.
4. Using CLI call `php app_fix.php` _(accidental repeated calls won't cause problems)_

## 2. app_restore.php

This is used to restore `vendor_schedule` to its previous state. Logic is inside RestoreSchedule class

### Problem analysis:
- We have saved `vendor_schedule` AUTO_INCREMENT value prior to applying the fix.
- Erase all values with id>= AUTO_INCREMENT from `vendor_schedule`, these are entries temporary transferred from
 `vendor_special_day` table.
- Get all the original values from backup table `vendor_schedule_backup` back to `vendor_schedule`.
- Set the AUTO_INCREMENT back to where it was before the fix.
- ID's and all other values in `vendor_schedule` table are exactly the same as they were before the fix.

### Execution
1. Using CLI call `php app_restore.php` _(accidental repeated calls won't cause problems)_