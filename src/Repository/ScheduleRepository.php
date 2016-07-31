<?php

namespace Foodora\Repository;

use PDO;

/**
 * SQL repository class
 *
 * Class ScheduleRepository
 * @package Foodora\Repository
 */
class ScheduleRepository
{
    /** @var PDO */
    private $db;

    //vendor_schedule autoincrement value before the fix
    private $autoIncrement;

    /**
     * ScheduleRepository constructor.
     * @param PDO $db_instance
     * @throws \Exception when auto increment value is not an integer
     */
    public function __construct(PDO $db_instance)
    {
        $autoIncrement = require __DIR__ . '/../../config/auto_increment.php';

        if (!is_numeric($autoIncrement) || empty($autoIncrement)) {
            throw new \Exception(__CLASS__ . 'autoIncrement must be an integer greater than zero.');
        }
        $this->db = $db_instance;
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * Backup records from vendor_schedule
     * For every records in vendor_schedule that is found in vendor_special_day,
     * that has the same weekday record - get a backup.
     */
    public function backupConflictingSchedules()
    {
        $this->db->exec('INSERT INTO vendor_schedule_backup
                        SELECT vendor_schedule.*
                        FROM vendor_schedule LEFT JOIN vendor_special_day 
                        ON (vendor_schedule.vendor_id=vendor_special_day.vendor_id)
                        WHERE (weekday(vendor_special_day.special_date)+1) = vendor_schedule.weekday AND 
                              (vendor_special_day.special_date BETWEEN "2015-12-21" AND "2015-12-27") 
        ');
    }

    /**
     * Delete values from vendor_schedule that were transferred to backup
     */
    public function deleteConflictingEntries()
    {
        $this->db->exec('DELETE vendor_schedule FROM vendor_schedule RIGHT JOIN vendor_schedule_backup 
                         ON (vendor_schedule.id=vendor_schedule_backup.id)
        ');
    }

    /**
     * Transfer vendor_special_day data to vendor_schedule table
     */
    public function insertFixIntoSchedule()
    {
        $this->db->exec('INSERT INTO vendor_schedule(vendor_id, weekday, all_day, start_hour, stop_hour) 
                        SELECT vendor_id, (weekday(special_date)+1), all_day, start_hour, stop_hour
                        FROM vendor_special_day 
                        WHERE vendor_special_day.event_type!="closed" AND 
                        (vendor_special_day.special_date BETWEEN "2015-12-21" AND "2015-12-27")
        ');
    }

    /**
     * Restore vendor_schedule.
     *
     * 1) Delete ported data from vendor_special_day
     * 2) Transfer data from vendor_schedule_backup
     */
    public function restoreSchedule()
    {
        $this->db->exec('DELETE FROM vendor_schedule WHERE id>=' . $this->autoIncrement);
        $this->db->exec('INSERT INTO vendor_schedule SELECT * FROM vendor_schedule_backup');
        $this->db->exec('ALTER TABLE vendor_schedule AUTO_INCREMENT=' . $this->autoIncrement);
    }

    /**
     * Returns TRUE if backup table is empty, FALSE if not
     *
     * @return boolean
     * @throws \PDOException table does not exist or any exception.
     */
    public function isBackupEmpty()
    {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) AS cnt FROM vendor_schedule_backup');
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res['cnt'] != 0) {
                return false;
            }
        } catch (\PDOException $e) {
            echo __CLASS__ . ' problem with backup table "vendor_schedule_backup", check if it exists.' . PHP_EOL;
        }

        return true;
    }

    /**
     * Create vendor_schedule_backup table based on vendor_schedule
     */
    public function createBackupTable()
    {
        $this->db->exec('CREATE TABLE IF NOT EXISTS vendor_schedule_backup LIKE vendor_schedule');

    }
}
