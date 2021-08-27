<?php

namespace MailBundle\Component\Archive\Generator\MailingList;

use CommonBundle\Component\Util\File\TmpFile;
use DateTime;
use Doctrine\ORM\EntityManager;
use ZipArchive;

/**
 * A class that can be used to generate a ZIP from a given array of
 * mailing lists.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Zip
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager = null;

    /**
     * @var array The array containing the mailinglists
     */
    private $lists;

    /**
     * @param EntityManager $entityManager The entityManager
     * @param array         $lists         The array containing the mailinglists
     */
    public function __construct(EntityManager $entityManager, array $lists)
    {
        $this->entityManager = $entityManager;
        $this->lists = $lists;
    }

    /**
     * Generate an archive to download.
     *
     * @param TmpFile $archive The file to write to
     */
    public function generateArchive(TmpFile $archive)
    {
        $zip = new ZipArchive();
        $now = new DateTime();

        $zip->open($archive->getFileName(), ZipArchive::CREATE);
        $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
        $zip->close();

        foreach ($this->lists as $list) {
            $entries = $this->entityManager
                ->getRepository('MailBundle\Entity\MailingList\Entry')
                ->findByList($list);

            $entriesString = '';
            foreach ($entries as $entry) {
                $entriesString .= $entry->getEmailAddress() . PHP_EOL;
            }

            $zip->open($archive->getFileName(), ZipArchive::CREATE);
            $zip->addFromString($list->getName(), $entriesString);
            $zip->close();
        }
    }
}
