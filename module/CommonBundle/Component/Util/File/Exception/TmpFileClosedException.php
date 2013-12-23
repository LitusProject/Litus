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

namespace CommonBundle\Component\Util\File\Exception;

use CommonBundle\Component\Util\File\TmpFile;

/**
 * This exception is thrown when a handle to a tempory file, that is already closed,
 * is used.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class TmpFileClosedException extends \Exception
{
}
