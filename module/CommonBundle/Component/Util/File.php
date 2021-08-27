<?php

namespace CommonBundle\Component\Util;

/**
 * Utility class containing methods used for common actions on files.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class File
{
    /**
     * Returns the real filename of the given file.
     *
     * @static
     * @param  string $filename The filename as it is used in the code, with '/' as directory separator
     * @return string|null
     */
    public static function getRealFilename($filename)
    {
        if ($filename === null) {
            return null;
        }

        // Skip the replace, saves time
        if (DIRECTORY_SEPARATOR === '/') {
            return $filename;
        }

        return str_replace('/', DIRECTORY_SEPARATOR, $filename);
    }
}
