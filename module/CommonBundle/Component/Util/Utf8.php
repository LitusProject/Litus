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
 * Provides a few utility methods to handle UTF-8.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Utf8
{
    /**
     * Convert a UTF-8 string to HTML.
     *
     * @static
     * @param string $utf8 The string in UTF-8 charset
     * @param boolean $encodeTags True will convert "<" to "&lt;", default value is false
     * @return string
     * @throws \CommonBundle\Component\Util\Exception\InvalidArgumentException The given first parameter was not a string
     */
    public static function utf8ToHtml($utf8, $encodeTags = false)
    {
        if ($utf8 === null)
            return null;

        if (!is_string($utf8)) {
            throw new Exception\InvalidArgumentException(
                'Expected a string as first parameter, not ' . gettype($utf8)
            );
        }

        $result = '';
        for ($i = 0; $i < strlen($utf8); $i++) {
            $char = $utf8[$i];
            $ascii = ord($char);
            if ($ascii < 128) {
                // One-byte character
                $result .= ($encodeTags) ? htmlentities($char) : $char;
            } else if ($ascii < 192) {
                // Non-utf8 character or not a start byte
            } else if ($ascii < 224) {
                // Two-byte character
                $ascii1 = ord($utf8[$i+1]);
                $unicode = (15 & $ascii) * 64 + (63 & $ascii1);
                $result .= '&#x' . dechex($unicode) . ';';
                $i++;
            } else if ($ascii < 240) {
                // Three-byte character
                $ascii1 = ord($utf8[$i+1]);
                $ascii2 = ord($utf8[$i+2]);
                $unicode = (15 & $ascii) * 4096 + (63 & $ascii1) * 64 + (63 & $ascii2);
                $result .= '&#x' . dechex($unicode) .';';
                $i += 2;
            } else if ($ascii < 248) {
                // Four-byte character
                $ascii1 = ord($utf8[$i+1]);
                $ascii2 = ord($utf8[$i+2]);
                $ascii3 = ord($utf8[$i+3]);
                $unicode = (15 & $ascii) * 262144 + (63 & $ascii1) * 4096 + (63 & $ascii2) * 64 + (63 & $ascii3);
                $result .= '&#x' . dechex($unicode) . ';';
                $i += 3;
            }
        }

        return $result;
    }
}
