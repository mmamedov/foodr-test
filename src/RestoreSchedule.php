<?php

namespace Foodora;

use Foodora\Repository\ScheduleRepository;

/**
 * Restore data to vendor_schedule table
 *
 * Class RestoreSchedule
 * @package Foodora
 */
class RestoreSchedule
{
    /** @var  \PDO */
    private $db;

    /** @var  ScheduleRepository */
    private $rSchedule;

    /**
     * Initiate Database connection
     * RestoreSchedule constructor.
     */
    public function __construct()
    {
        //setup database connection
        $db_pdo = new PDOService();
        $this->db = $db_pdo->getConnection();

        //initiate repository
        $this->rSchedule = new ScheduleRepository($this->db);
    }

    /**
     * Restore data
     */
    public function process()
    {
        //make sure backup table exists and is not empty
        //table: vendor_schedule_backup
        if ($this->rSchedule->isBackupEmpty()) {
            throw new \Exception('Backup table does not exist or is empty. Operation halted.');
        }

        try {
            $this->db->beginTransaction();

            //restore schedule
            $this->rSchedule->restoreSchedule();

            //commit changes
            if ($this->db->commit()) {
                echo 'Vendor schedule restored!' . PHP_EOL;
            } else {
                echo 'Error: Vendor schedule was not restored.' . PHP_EOL;
            }
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo __CLASS__ . ' process failed ' . $e->getMessage() . PHP_EOL;
        }

        return null;
    }
}
