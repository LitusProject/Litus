<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
    /**
     * @param \CommonBundle\Component\Util\File\TmpFile $tmpFile The file that caused the exception
     */    
    public function __construct(TmpFile $tmpFile)
    {
        parent::__construct(
            $tmpFile->getFilename() . ' has already been closed'
        );
    }
}
