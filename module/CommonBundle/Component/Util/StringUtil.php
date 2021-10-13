<?php

namespace CommonBundle\Component\Util;

/**
 * Utility class containing methods used for common actions on strings.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class StringUtil
{
    /**
     * Truncates a string preserving HTML tags
     *
     * @static
     * @param  string  $string The string that will be truncated
     * @param  integer $length
     * @return string
     */
    public static function truncate($string, $length, $suffix = '...')
    {
        $i = 0;
        $tags = array();

        preg_match_all('/<[^>]+>([^<]*)/', $string, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        if (is_array($m)) {
            foreach ($m as $o) {
                if ($o[0][1] - $i >= $length) {
                    break;
                }
                $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
                if ($t[0] != '/') {
                    $tags[] = $t;
                } elseif (end($tags) == substr($t, 1)) {
                    array_pop($tags);
                }
                $i += $o[1][1] - $o[0][1];
            }
        }

        return substr($string, 0, $length = min(strlen($string), $length + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '') . (strlen($string) > $length ? $suffix : '');
    }

    /**
     * Truncates a string removing HTML tags
     *
     * @static
     * @param  string  $string The string that will be truncated
     * @param  integer $length
     * @return string
     */
    public static function truncateNoHtml($string, $length, $suffix = '...')
    {
        $string = strip_tags($string);

        return substr($string, 0, $length = min(strlen($string), $length)) . (strlen($string) > $length ? $suffix : '');
    }

    /**
     * Changes an 'underscored' string to CamelCase.
     *   e.g.: 'a_bcd-ef' becomes 'ABcdEf'
     *
     * @static
     * @param  string $text
     * @return string
     */
    public static function underscoredToCamelCase($text)
    {
        $text = str_replace(array('-', '_'), ' ', $text);

        return str_replace(' ', '', ucwords($text));
    }
}
