<?php

namespace FormBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File as FileUtil;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Entity\General\Language;
use DateTime;
use Doctrine\ORM\EntityManager;
use ZipArchive;

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
    public function __construct(TmpFile $tmpFile, EntityManager $entityManager, Language $language, $entries, $array = false)
    {
        $zip = new ZipArchive();
        $now = new DateTime();

        $zip->open($tmpFile->getFileName(), ZipArchive::CREATE);
        $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
        $zip->close();

        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('form.file_upload_path') . '/';

        if ($array == false) {
            foreach ($entries as $entry) {
                $zip->open($tmpFile->getFileName(), ZipArchive::CREATE);
                $zip->addFile(
                    $filePath . $entry->getValue(),
                    $entry->getField()->getLabel($language) . '_' . $entry->getFormEntry()->getPersonInfo()->getFullName() . '_' . $entry->getFormEntry()->getId() . '_' . $entry->getReadableValue()
                );
                $zip->close();
            }
        } else {
            foreach ($entries as $invoice) {
                $file = FileUtil::getRealFilename(
                    $entityManager->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.file_path') . '/invoices/'
                    . $invoice->getInvoiceNumberPrefix() . '/'
                    . $invoice->getInvoiceNumber() . '.pdf'
                );

                $zip->open($tmpFile->getFileName(), ZipArchive::CREATE);
                $zip->addFile($file);
                $zip->close();
            }
        }
        $zip->close();
    }
}
