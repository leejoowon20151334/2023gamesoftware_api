<?php

namespace src\System;

use src\Util\Memcached;

class Cache extends Memcached
{
    protected static $instance = null;

    public static function GetInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self(MEMCACHED_CONFIG['api']);
        }

        return self::$instance;
    }
}
