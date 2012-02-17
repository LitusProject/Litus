<?php

namespace CommonBundle\Component\Generator;

use CommonBundle\Component\Util\File\TmpFile,
	Doctrine\ORM\EntityManager;

abstract class DocumentGenerator
{
    /**
     * @var Litus\Repository\Config\Config
     */
    private static $_configs;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_entityManager;
    
    /**
     * @var CommonBundle\Component\Util\TmpFile
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

	public function __construct(EntityManager $entityManager, $xsl, $pdf)
    {
        if(($xsl === null) || !is_string($xsl))
            throw new \InvalidArgumentException('Invalid xsl');
            
        if(($pdf === null) || !is_string($pdf))
            throw new \InvalidArgumentException('Invalid pdf');

        $this->_xsl = $xsl;
        $this->_pdf = $pdf;
        $this->_xml = new TmpFile();
        $this->_entityManager = $entityManager;
    }
	    
    /**
     * @static
     * @return Litus\Repository\General\Config
     */
    protected function _getConfigRepository()
    {
        if(self::$_configs === null) {
            self::$_configs = $this->getEntityManager()
            	->getRepository('CommonBundle\Entity\General\Config');
        }
        return self::$_configs;
    }

    /**
     * @static
     * @param $address string
     * @return string
     */
    protected function formatAddress($address)
    {
        return str_replace(',', '<br/>', $address);
    }

    public function generate()
    {
        $this->_generateXml($this->_xml);
        $this->generatePdf();
    }

    protected abstract function _generateXml(TmpFile $file);

    protected function generatePdf()
    {
        $xml = $this->_xml->getFilename();

        $pdfDir = dirname($this->_pdf);
        if(!file_exists($pdfDir)) {
            if(!mkdir($pdfDir, 0770))
                throw new \RuntimeException('Failed to create directory ' . $pdfDir);
        }

        $resultValue = 0;
        $command = 'fop -xsl ' . $this->_xsl . ' -xml ' . $xml . ' ' . $this->_pdf;
        $result = system($command, $resultValue);
        
        if($resultValue != 0)
            throw new \RuntimeException($command . ' failed with return value ' . $resultValue . ': ' . $result);
    }
    
    public function getEntityManager()
    {
    	return $this->_entityManager;
    }

}
