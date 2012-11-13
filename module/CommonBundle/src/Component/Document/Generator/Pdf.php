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

namespace CommonBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile,
    Doctrine\ORM\EntityManager;

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
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager;

    /**
     * @var string The path to the document's XSL file
     */
    protected $_xslPath;

    /**
     * @var \CommonBundle\Component\Util\TmpFile A tempory file which holds the generated XML structure
     */
    protected $_xmlFile;

    /**
     * @var string The
     */
    protected $_pdfPath;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $xslPath The path to the document's XSL file
     * @param string $pdfPath The path all PDF's should be exported to
     */
    public function __construct(EntityManager $entityManager, $xslPath, $pdfPath)
    {
        if(($xslPath === null) || !is_string($xslPath))
            throw new \InvalidArgumentException('Invalid xsl');

        if(($pdfPath === null) || !is_string($pdfPath))
            throw new \InvalidArgumentException('Invalid pdf');

        $this->_entityManager = $entityManager;

        $this->_xslPath = $xslPath;
        $this->_pdfPath = $pdfPath;

        $this->_xmlFile = new TmpFile();
    }

    /**
     * Returns our configuration repository.
     *
     * @return \CommonBundle\Repository\General\Config
     */
    public function getConfigRepository()
    {
        return $this->_entityManager->getRepository('CommonBundle\Entity\General\Config');
    }

    /**
     * Format
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
            $this->_xmlFile
        );

        $this->generatePdf();
    }

    /**
     * Generate the document's XML structure.
     *
     * @param \CommonBundle\Component\Util\TmpFile $xmlFile A tempory file which holds the generated XML structure
     * @return void
     */
    abstract protected function generateXml(TmpFile $xmlFile);

    /**
     * Generate the PDF document using the specified XSL and XML files, using FOP.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function generatePdf()
    {
        $xmlPath = $this->_xmlFile->getFilename();

        $pdfDir = dirname($this->_pdfPath);
        if (!file_exists($pdfDir)) {
            if(!mkdir($pdfDir, 0770))
                throw new \RuntimeException('Failed to create the PDF directory');
        }

        $resultValue = 0;

        $fop_command = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('fop_command');;

        $result = system(
            escapeshellcmd($fop_command . ' -xsl ' . $this->_xslPath . ' -xml ' . $xmlPath . ' ' . $this->_pdfPath), $resultValue
        );

        if ($resultValue != 0)
            throw new \RuntimeException('The FOP command failed with return value ' . $resultValue);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }
}
