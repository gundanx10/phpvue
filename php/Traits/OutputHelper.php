<?php

namespace App\Traits;

trait OutputHelper
{
    public static function is_debug_enable()
    {
        return in_array('--debug', $_SERVER['argv'], true);
    }

    public static function debug_output($info)
    {
        if (self::is_debug_enable()) {
            dump($info);
        }
    }
}
