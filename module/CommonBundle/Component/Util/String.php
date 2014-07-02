<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Util;

/**
 * Utility class containing methods used for common actions on strings.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class String
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
                if($o[0][1] - $i >= $length)
                    break;
                $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
                if($t[0] != '/')
                    $tags[] = $t;
                elseif(end($tags) == substr($t, 1))
                    array_pop($tags);
                $i += $o[1][1] - $o[0][1];
            }
        }

        return substr($string, 0, $length = min(strlen($string),  $length + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '') . (strlen($string) > $length ? $suffix : '');
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
}
