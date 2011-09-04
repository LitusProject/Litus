<?php

namespace Litus\Util;

class UTF8
{
    /**
     * @static
     * @param string $utf8 the string in utf-8 charset
     * @param boolean $encodeTags true will convert "<" to "&lt;"
     * @return string
     */
    public static function utf8toHtml($utf8, $encodeTags)
    {
        if($utf8 === null)
            return null;
        if(!is_string($utf8))
            throw new \InvalidArgumentException('UTF8::utf8toHtml expects a string as first parameter, not ' . gettype($utf8));

        $result = '';
        for ($i = 0; $i < strlen($utf8); $i++) {
            $char = $utf8[$i];
            $ascii = ord($char);
            if ($ascii < 128) {
                // one-byte character
                $result .= ($encodeTags) ? htmlentities($char) : $char;
            } else if ($ascii < 192) {
                // non-utf8 character or not a start byte
            } else if ($ascii < 224) {
                // two-byte character
//                $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
                $ascii1 = ord($utf8[$i+1]);
                $unicode = (15 & $ascii) * 64 + (63 & $ascii1);
                $result .= '&#x' . dechex($unicode) . ';';
                $i++;
            } else if ($ascii < 240) {
                // three-byte character
                $ascii1 = ord($utf8[$i+1]);
                $ascii2 = ord($utf8[$i+2]);
                $unicode = (15 & $ascii) * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
                $result .= '&#x' . dechex($unicode) .';';
                $i += 2;
            } else if ($ascii < 248) {
                // four-byte character
                $ascii1 = ord($utf8[$i+1]);
                $ascii2 = ord($utf8[$i+2]);
                $ascii3 = ord($utf8[$i+3]);
                $unicode = (15 & $ascii) * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
                $result .= '&#x' . dechex($unicode) . ';';
                $i += 3;
            }
        }
        return $result;
    }

}
