<?php

namespace CommonBundle\Component\Generator;

use CommonBundle\Component\Util\TmpFile,
	Doctrine\ORM\EntityManager;

abstract class DocumentGenerator
{
    /**
     * @var \Litus\Repository\Config\Config
     */
    private static $_configs;
    
    private $_entityManager;
    
    /**
     * @var \CommonBundle\Component\Util\TmpFile
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
     * @static
     * @return Litus\Repository\General\Config
     */
    protected static function _getConfigRepository()
    {
        if(self::$_configs === null) {
            /** @var \Doctrine\ORM\EntityManager $_em  */
            self::$_configs = $this->getEntityManager()->getRepository('Litus\Entity\General\Config');
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

    public function __construct($xsl, $pdf)
    {
        if(($xsl === null) || !is_string($xsl))
            throw new \InvalidArgumentException('Invalid xsl');
            
        if(($pdf === null) || !is_string($pdf))
            throw new \InvalidArgumentException('Invalid pdf');

        $this->_xsl = $xsl;
        $this->_pdf = $pdf;
        $this->_xml = new TmpFile();
    }

    public function generate()
    {
        $this->_generateXml($this->_xml);
        $this->_generatePdf();
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
        $result = system($command, &$resultValue);
        
        if($resultValue != 0)
            throw new \RuntimeException($command . ' failed with return value ' . $resultValue . ': ' . $result);
    }
    
    public function setEntityManager(EntityManager $entityManager)
    {
    	$this->_entityManager = $entityManager;
    }
    
    public function getEntityManager()
    {
    	return $this->_entityManager;
    }

}
