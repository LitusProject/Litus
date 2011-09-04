<?php

namespace Litus\Br;

use \Litus\Util\TmpFile;

use \Zend\Registry;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

abstract class DocumentGenerator
{
     /**
     * @var \Litus\Repository\Config\Config
     */
    private static $_configs;

    /**
     * @static
     * @return Litus\Repository\Config\Config
     */
    protected static function _getConfigRepository()
    {
        if(self::$_configs === null) {
            /** @var \Doctrine\ORM\EntityManager $_em  */
            $_em = Registry::get(DoctrineResource::REGISTRY_KEY);
            self::$_configs = $_em->getRepository('Litus\Entity\Config\Config');
        }
        return self::$_configs;
    }

    protected static function _formatAddress($address)
    {
        return str_replace(',', '<br/>', $address);
    }

    /**
     * @var \Litus\Util\TmpFile
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

    protected function _generatePdf()
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

}
