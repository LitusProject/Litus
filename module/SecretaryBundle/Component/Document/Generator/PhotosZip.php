<?php

namespace SecretaryBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile;
use DateTime;
use Doctrine\ORM\EntityManager;
use ZipArchive;

/**
 * Create a zip with the photo's of the given promotions
 *
 * @author Mathijs Cuppens
 */
class PhotosZip
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager = null;

    /**
     * @var array The array containing the promotions
     */
    private $promotions;

    /**
     * @param EntityManager $entityManager The entityManager
     * @param array         $promotions    The array containing the promotions
     */
    public function __construct(EntityManager $entityManager, array $promotions)
    {
        $this->entityManager = $entityManager;
        $this->promotions = $promotions;
    }

    /**
     * Generate an archive to download.
     *
     * @param  TmpFile $archive The file to write to
     * @return null
     */
    public function generateArchive(TmpFile $archive)
    {
        $zip = new ZipArchive();
        $now = new DateTime();

        $zip->open($archive->getFileName(), ZipArchive::CREATE);
        $zip->addFromString('GENERATED', $now->format('YmdHi') . PHP_EOL);
        $zip->close();

        $filePath = 'public' . $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.profile_path') . '/';

        foreach ($this->promotions as $promotion) {
            if ($promotion->getAcademic()->getPhotoPath()) {
                $extension = $this->getExtension($filePath . $promotion->getAcademic()->getPhotoPath());

                $zip->open($archive->getFileName(), ZipArchive::CREATE);
                $zip->addFile(
                    $filePath . $promotion->getAcademic()->getPhotoPath(),
                    $promotion->getAcademic()->getFirstName() . '_' . $promotion->getAcademic()->getLastName() . $extension
                );
                $zip->close();
            }
        }
    }

    /**
     * returns the extension of the given file. Based on the constant int output of exif_imagetype
     * @param  string $fileName
     * @return string
     */
    private function getExtension($fileName)
    {
        $fileType = exif_imagetype($fileName);
        $result = '';

        switch ($fileType) {
            case 1:
                $result = '.gif';
                break;
            case 2:
                $result = '.jpeg';
                break;
            case 3:
                $result = '.png';
                break;
            case 5:
                $result = '.psd';
                break;
            case 6:
                $result = '.bmp';
                break;
        }

        return $result;
    }
}
