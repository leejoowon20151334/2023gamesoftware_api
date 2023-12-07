<?php

namespace src\System;

use src\Util\MySQL;

/**
 * Database provides DB initialization function and a getter function for the
 * singleton Database instance.
 */
class DataSourceDB extends MySQL
{
    protected static $instance = null;

    /**
     * Return the singleton Database Instance.
     *
     * @return DataSourceDB The singleton Databse instance.
     */
    public static function GetInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['data_source']);
        }
        return self::$instance;
    }

    public static function GetUpdateInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['update']);
        }

        return self::$instance;
    }

    public static function GetOldDBInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['oldDB']);
        }

        return self::$instance;
    }

    public static function GetServiceBetaDBInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['serviceDB-beta']);
        }

        return self::$instance;
    }

    public static function GetServiceLiveDBInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['serviceDB-live']);
        }

        return self::$instance;
    }

    public static function GetRealtorLiveDBInstance($forceConnect = false)
    {
        if (self::$instance === null || $forceConnect != false) {
            self::$instance = null;
            self::$instance = new self(DB_CONFIG['realtor-live']);
        }

        return self::$instance;
    }
}
