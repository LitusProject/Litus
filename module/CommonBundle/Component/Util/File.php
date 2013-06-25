<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

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
     * @param string $filename The filename as it is used in the code, with '/' as directory separator
     * @return string
     */
    public static function getRealFilename($filename)
    {
        if ($filename === null)
            return null;

        // Skip the replace, saves time
        if (DIRECTORY_SEPARATOR === '/')
            return $filename;

        return str_replace('/', DIRECTORY_SEPARATOR, $filename);
    }

}
