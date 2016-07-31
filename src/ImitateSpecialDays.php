<?php

namespace Foodora;

use Foodora\Repository\ScheduleRepository;

/**
 * Ports data from vendor_special_day to vendor_schedule.
 * Imitates Special Days feature using regular vendor schedule table.
 *
 * Class ImitateSpecialDays
 * @package Foodora
 */
class ImitateSpecialDays
{
    /** @var  \PDO */
    private $db;

    /** @var  ScheduleRepository */
    private $rSchedule;

    /**
     * Initiate Database connection
     * ImmitateSpecialDays constructor.
     */
    public function __construct()
    {
        //setup database connection
        $db_pdo = new PDOService();
        $this->db = $db_pdo->getConnection();

        //initiate repository
        $this->rSchedule = new ScheduleRepository($this->db);

        //create backup table if not exists
        $this->rSchedule->createBackupTable();
    }

    /**
     * Main entry function, starts the fix process
     */
    public function process()
    {
        //make sure backup table exists and is empty, to avoid accidentally applying the fix twice
        if ($this->rSchedule->isBackupEmpty() !== true) {
            throw new \Exception('Backup table does not exist or not empty. Operation halted.');
        }

        //begin data transfer
        try {
            $this->db->beginTransaction();

            //backup and delete conflicting schedules from vendor_schedule
            $this->rSchedule->backupConflictingSchedules();
            $this->rSchedule->deleteConflictingEntries();

            //transfer special days into regular days
            $this->rSchedule->insertFixIntoSchedule();

            //commit changes
            if ($this->db->commit()) {
                echo 'Special days transfer completed!' . PHP_EOL;
            } else {
                echo 'Error: Special days transfer was not completed.' . PHP_EOL;
            }
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo __CLASS__ . ' process failed ' . $e->getMessage() . PHP_EOL;
        }

        return null;
    }
}
