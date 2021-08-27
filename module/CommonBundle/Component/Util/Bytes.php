<?php

namespace CommonBundle\Component\Util;

/**
 * Utility class containing methods used for dealing with bytes.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Bytes
{
    /**
     * @var array
     */
    const UNITS = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

    /**
     * Formats the specified number of bytes as a human readable string.
     *
     * @param  integer $bytes    The number of bytes that should be formatted
     * @param  integer $decimals The number of decimal places
     * @return string
     */
    public static function format($bytes, $decimals = 2)
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf('%.' . $decimals . 'f', $bytes / pow(1024, $factor)) . @self::UNITS[$factor];
    }
}
