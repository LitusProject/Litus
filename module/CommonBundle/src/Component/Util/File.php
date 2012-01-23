<?php

namespace Litus\Util;

/**
 * @author Bram Gotink
 */
class File
{

    /**
     * Returns the real filename of the given file.
     *
     * @static
     * @param $filename string the filename as used in the code, with '/' as directory separator
     * @return string|null if $filename is null, the result is null, else the result will be $filename with all '/'
     *                     replaced by DIRECTORY_SEPARATOR
     *///This function allows us to use '/' as file separator and then change it into this
    public static function getRealFilename($filename)
    {
        if ($filename === null)
            return null;

        if (DIRECTORY_SEPARATOR === '/')
            // skip the replace, saves time
            return $filename;

        return str_replace('/', DIRECTORY_SEPARATOR, $filename);
    }

}
