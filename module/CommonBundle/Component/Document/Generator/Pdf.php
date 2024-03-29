<?php

namespace CommonBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use RuntimeException;

/**
 * This class provides a container to create documents
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Pdf
{
    /**
     * @var EntityManager The EntityManager instance
     */
    private $entityManager;

    /**
     * @var string The path to the document's XSL file
     */
    protected $xslPath;

    /**
     * @var TmpFile A tempory file which holds the generated XML structure
     */
    protected $xmlFile;

    /**
     * @var string The path where the pdf will be generated
     */
    protected $pdfPath;

    /**
     * @var String The possible languages for a generated pdf
     */
    const ENGLISH = 'en';
    const DUTCH = 'nl';

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param string        $xslPath       The path to the document's XSL file
     * @param string        $pdfPath       The path all PDF's should be exported to
     */
    public function __construct(EntityManager $entityManager, $xslPath, $pdfPath)
    {
        if (($xslPath === null) || !is_string($xslPath)) {
            throw new InvalidArgumentException('Invalid XSL');
        }

        if (($pdfPath === null) || !is_string($pdfPath)) {
            throw new InvalidArgumentException('Invalid PDF');
        }

        $this->entityManager = $entityManager;

        $this->xslPath = $xslPath;
        $this->pdfPath = $pdfPath;

        $this->xmlFile = new TmpFile();
    }

    /**
     * Returns our configuration repository.
     *
     * @return \CommonBundle\Repository\General\Config
     */
    public function getConfigRepository()
    {
        return $this->entityManager->getRepository('CommonBundle\Entity\General\Config');
    }

    /**
     * Format an address.
     *
     * @param $address string
     * @return string
     */
    protected function formatAddress($address)
    {
        return str_replace(',', '<br/>', $address);
    }

    /**
     * Generate the document.
     *
     * @return void
     */
    public function generate()
    {
        $this->generateXml(
            $this->xmlFile
        );

        $this->generatePdf();
    }

    /**
     * Generate the document's XML structure.
     *
     * @param  TmpFile $xmlFile A tempory file which holds the generated XML structure
     * @return void
     */
    abstract protected function generateXml(TmpFile $xmlFile);

    /**
     * Generate the PDF document using the specified XSL and XML files, using FOP.
     *
     * @return void
     * @throws RuntimeException
     */
    protected function generatePdf()
    {
        $xmlPath = $this->xmlFile->getFilename();

        $pdfDir = dirname($this->pdfPath);

        if (!file_exists($pdfDir)) {
            if (!mkdir($pdfDir, 0770, true)) {
                throw new RuntimeException('Failed to create the PDF directory');
            }
        }

        $resultValue = 0;

        $fopCommand = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fop_command');

        system(
            escapeshellcmd($fopCommand . ' -q -xsl ' . $this->xslPath . ' -xml ' . $xmlPath . ' ' . $this->pdfPath),
            $resultValue
        );

        if ($resultValue !== 0) {
            throw new RuntimeException('The FOP command failed with return value ' . $resultValue);
        }
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
