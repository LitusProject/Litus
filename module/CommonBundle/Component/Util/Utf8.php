<?php

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
     * @param  string  $utf8       The string in UTF-8 charset
     * @param  boolean $encodeTags True will convert "<" to "&lt;", default value is false
     * @return string|null
     * @throws Exception\InvalidArgumentException The given first parameter was not a string
     */
    public static function utf8ToHtml($utf8, $encodeTags = false)
    {
        if ($utf8 === null) {
            return null;
        }

        if (!is_string($utf8)) {
            throw new Exception\InvalidArgumentException(
                'Expected a string as first parameter, not ' . gettype($utf8)
            );
        }

        $result = '';
        $len = strlen($utf8);
        for ($i = 0; $i < $len; $i++) {
            $char = $utf8[$i];
            $ascii = ord($char);
            if ($ascii < 128) {
                // One-byte character
                $result .= $encodeTags ? htmlentities($char) : $char;
            } elseif ($ascii >= 192 && $ascii < 224) {
                // Two-byte character
                $ascii1 = ord($utf8[$i + 1]);
                $unicode = (15 & $ascii) * 64 + (63 & $ascii1);
                $result .= '&#x' . dechex($unicode) . ';';
                $i++;
            } elseif ($ascii < 240) {
                // Three-byte character
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $unicode = (15 & $ascii) * 4096 + (63 & $ascii1) * 64 + (63 & $ascii2);
                $result .= '&#x' . dechex($unicode) . ';';
                $i += 2;
            } elseif ($ascii < 248) {
                // Four-byte character
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $ascii3 = ord($utf8[$i + 3]);
                $unicode = (15 & $ascii) * 262144 + (63 & $ascii1) * 4096 + (63 & $ascii2) * 64 + (63 & $ascii3);
                $result .= '&#x' . dechex($unicode) . ';';
                $i += 3;
            }
        }

        return $result;
    }
}
