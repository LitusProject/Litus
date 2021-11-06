<?php

namespace CommonBundle\Component\Util;

/**
 * Utility class containing methods used for common actions on URLs.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Url
{
    /**
     * Creates a clean URL slug from the given string.
     *
     * @static
     * @param  string $string    The string that will be cleaned
     * @param  string $delimiter The delimiter used to replace spaces
     * @return string
     */
    public static function createSlug($string, $delimiter = '-')
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace('/[\/_|+ -]+/', $delimiter, $clean);

        return $clean;
    }
}
