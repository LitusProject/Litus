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

namespace FormBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile as TmpFile,
    CommonBundle\Entity\General\Language,
    DateTime,
    Doctrine\ORM\EntityManager,
    ZipArchive;

/**
 * Zip
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Zip
{
    /**
     * @param TmpFile       $tmpFile
     * @param EntityManager $entityManager
     * @param Language      $language
     * @param array         $entries
     */
    public function __construct(TmpFile $tmpFile, EntityManager $entityManager, Language $language, $entries)
    {
        $zip = new ZipArchive();
        $now = new DateTime();

        $zip->open($tmpFile->getFileName(), ZIPARCHIVE::CREATE);
        $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
        $zip->close();

        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path') . '/';

        foreach ($entries as $entry) {
            $zip->open($tmpFile->getFileName(), ZIPARCHIVE::CREATE);
            $zip->addFile(
                $filePath . $entry->getValue(),
                $entry->getField()->getLabel($language) . '_' . $entry->getFormEntry()->getPersonInfo()->getFullName() . '_' . $entry->getFormEntry()->getId() . '_' . $entry->getReadableValue()
            );
            $zip->close();
        }
    }
}
