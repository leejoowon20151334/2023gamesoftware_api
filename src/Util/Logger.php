<?php

namespace src\Util;

/**
 * Logger provides functions for logging.
 */
class Logger
{
    /**
     * Prints information of an object and calling location.
     *
     * Optionally exit the program completely.
     *
     * @param mixed $obj    Object that need to print for inspect its value.
     * @param bool  $option When true, exit the program.
     */
    public static function Log($obj, $option = false)
    {
        print_r($obj);
        print_r(PHP_EOL);
        if ($option) {
            exit();
        }
    }

    /**
     * Prints information of an object.
     *
     * @param mixed $obj Object that need to print for inspect its value.
     */
    public static function EchoLog($obj)
    {
        print_r($obj);
        print_r(PHP_EOL);
        print_r(str_pad('', 4096));
        ob_flush();
        flush();
    }

    /**
     * Prints an error message with an object and calling location.
     * And Exit the program completely.
     *
     * @param array $obj Object that need to print for inspect its value.
     */
    public static function Error($obj = null)
    {
        print_r('[Error]' . xdebug_call_file() . ':' . xdebug_call_line() . PHP_EOL);

        if ($obj !== null) {
            print_r($obj);
            print_r(PHP_EOL);
        }

        exit();
    }
}
