<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace MailBundle\Component\Document\Generator\MailingList;

use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    DateTime,
    Doctrine\ORM\EntityManager;

class Csv
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @var array The array containing the mailinglists
     */
    private $_results;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The entityManager
     * @param arrays $lists The array containing the mailinglists
     */
    public function __construct(EntityManager $entityManager, array $results)
    {
        $this->_entityManager = $entityManager;
        $this->_results = $results;
    }

    /**
     * Generate a file to download.
     *
     * @param \CommonBundle\Component\Util\CsvFile $file The file to write to
     */
    public function generateDocument(CsvFile $file)
    {
        foreach($this->_results as $result) {
            $file->appendContent($result);
        }
    }
}
