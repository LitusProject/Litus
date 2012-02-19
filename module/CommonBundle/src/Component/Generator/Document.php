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
 
namespace CommonBundle\Component\Generator;

use CommonBundle\Component\Util\File\TmpFile,
	Doctrine\ORM\EntityManager;

/**
 * This class provides a container to create documents
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Document
{    
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager;
    
    /**
     * @var CommonBundle\Component\Util\TmpFile A temp
     */
    private $_xml;

    /**
     * @var string
     */
    private $_xsl;

    /**
     * @var string
     */
    private $_pdf;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
	 * @param string $xsl The XSL sheet
	 * @param string $pdf The name of the PDF file
	 */
	public function __construct(EntityManager $entityManager, $xsl, $pdf)
    {
        if(($xsl === null) || !is_string($xsl))
            throw new \InvalidArgumentException('Invalid xsl');
            
        if(($pdf === null) || !is_string($pdf))
            throw new \InvalidArgumentException('Invalid pdf');

		$this->_entityManager = $entityManager;
		        
        $this->_xsl = $xsl;
        $this->_pdf = $pdf;
        $this->_xml = new TmpFile();
    }
	    
    /**
     * Returns our configuration repository.
     *
     * @return Litus\Repository\General\Config
     */
    protected function getConfigRepository()
    {
        return $this->_entityManager->getRepository('CommonBundle\Entity\General\Config');
    }

    /**
     * 
     *
     * @param $address string
     * @return string
     */
    protected function formatAddress($address)
    {
        return str_replace(',', '<br/>', $address);
    }
	
	
    public function generate()
    {
        $this->generateXml($this->_xml);
        $this->generatePdf();
    }

    protected abstract function generateXml(TmpFile $file);

    protected function generatePdf()
    {
        $xml = $this->_xml->getFilename();

        $pdfDir = dirname($this->_pdf);
        if (!file_exists($pdfDir)) {
            if(!mkdir($pdfDir, 0770))
                throw new \RuntimeException('Failed to create directory ' . $pdfDir);
        }

        $resultValue = 0;
        $result = system(
        	escapeshellcmd('fop -xsl ' . $this->_xsl . ' -xml ' . $xml . ' ' . $this->_pdf), $resultValue
        );
        
        if ($resultValue != 0)
            throw new \RuntimeException($command . ' failed with return value ' . $resultValue . ': ' . $result);
    }
}
