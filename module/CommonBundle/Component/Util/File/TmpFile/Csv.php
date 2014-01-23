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

namespace CommonBundle\Component\Util\File\TmpFile;

/**
 * Extending the existing TmpFile class so that content is CSV formatted.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Csv extends \CommonBundle\Component\Util\File\TmpFile
{
    /**
     * Append a new array to the file.
     *
     * @param array $content The content that should be appended
     * @return void
     */
    public function appendContent($content)
    {
        $this->checkOpen();
        fputcsv($this->fileHandle, $content);
    }
}
