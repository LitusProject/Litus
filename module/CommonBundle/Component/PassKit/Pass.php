<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Component\PassKit;

use CommonBundle\Component\Util\File\TmpFile,
    DirectoryIterator,
    Doctrine\ORM\EntityManager,
    ZipArchive;

/**
 * This class can be used to generate Apple Pass Kit passes.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Pass
{
    /**
     * @var \CommonBundle\Component\Util\File\TmpFile The temporary file for the pass
     */
    private $_pass = null;

    /**
     * @var string The location of the image directory
     */
    private $_imageDirectory = '';

    /**
     * @var \CommonBundle\Component\Util\File\TmpFile The temporary file for the manifest
     */
    private $_manifest = null;

    /**
     * @var \CommonBundle\Component\Util\File\TmpFile The temporary file for the signature
     */
    private $_signature = null;

    /**
     * @var array The passes' languages
     */
    private $_languages = array();

    /**
     * @param \CommonBundle\Component\Util\File\TmpFile $pass The temporary file for the pass
     * @param string $imageDirectory The location of the image directory
     */
    public function __construct(TmpFile $pass, $imageDirectory)
    {
        $this->_pass = $pass;
        $this->_imageDirectory = $imageDirectory;

        $this->_manifest = new TmpFile();
        $this->_signature = new TmpFile();
    }

    /**
     * Add a new language to the pass.
     *
     * @param string $name The name of the language
     * @param array $strings The localised strings
     */
    public function addLanguage($name, array $strings)
    {
        $this->_languages[$name] = $strings;
        return $this;
    }

    /**
     * Creates the pass archive.
     *
     * @return void
     */
    public function createPass()
    {
        $this->_createManifest();

        $pass = new ZipArchive();
        $pass->open($this->_pass->getFilename(), ZipArchive::CREATE);
        $pass->addFromString('signature', $this->_createSignature());
        $pass->addFromString('manifest.json', $this->_manifest->getContent());
        $pass->addFromString('pass.json', $this->getJson());

        foreach ($this->_getImages() as $image)
            $pass->addFile($image, basename($image));

        $languages = array_keys($this->_languages);
        for ($i = 0; isset($languages[$i]); $i++) {
            $pass->addEmptyDir($languages[$i] . '.lproj');
            $pass->addFromString($languages[$i] . '.lproj/pass.strings', $this->_createLanguage($languages[$i]));
        }

        $pass->close();
    }

    /**
     * Get the pass' JSON directory.
     *
     * @return string
     */
    abstract protected function getJson();

    /**
     * Get the certicicate used to sign the pass.
     *
     * @return array
     */
    abstract protected function getCertificate();

    /**
     * Create the pass' manifest.
     *
     * @return void
     */
    private function _createManifest()
    {
        $hashes = array(
            'pass.json' => sha1($this->getJson())
        );

        foreach ($this->_getImages() as $image)
            $hashes[strtolower(basename($image))] = sha1(file_get_contents($image));

        $languages = array_keys($this->_languages);
        for ($i = 0; isset($languages[$i]); $i++)
            $hashes[$languages[$i] . '.lproj/strings.json'] = sha1($this->_createLanguage($languages[$i]));

        $this->_manifest->appendContent(json_encode($hashes));
    }

    /**
     * Create the pass' signature.
     *
     * @return string
     */
    private function _createSignature()
    {
        $certificate = $this->getCertificate();
        $readCertificates = array();

        if (openssl_pkcs12_read(file_get_contents($certificate['path']), $readCertificates, $certificate['password'])) {
            $certificateData = openssl_x509_read($readCertificates['cert']);
            $privateKey = openssl_pkey_get_private($readCertificates['pkey'], $certificate['password']);
        } else {
            throw new Exception\CouldNotReadCertificateException(
                'Failed to read the certificate file "' . $certificate['path'] . "'"
            );
        }

        openssl_pkcs7_sign(
            $this->_manifest->getFilename(),
            $this->_signature->getFilename(),
            $certificateData,
            $privateKey,
            array(),
            PKCS7_BINARY | PKCS7_DETACHED,
            'data/certificates/AppleWWDRCA.pem'
        );

        return $this->_convertPemToDer($this->_signature->getContent());
    }

    private function _createLanguage($name)
    {
        $strings = '';
        foreach ($this->_languages[$name] as $key => $value)
            $strings .= '"' . $key . '" = "' . $value . '";' . PHP_EOL;

        return $strings;
    }

    /**
     * Convert a certifcate in the PEM format to a DER.
     *
     * @param string $signature The PEM signature
     * @return string
     */
    private function _convertPemToDer($signature)
    {
        $signature = substr($signature, (strpos($signature, 'filename="smime.p7s"')+20));
        return base64_decode(trim(substr($signature, 0, strpos($signature, '------'))));
    }

    /**
     * Retrieves a list of all the files in the image directory.
     *
     * @return array
     */
    private function _getImages()
    {
        $directory = new DirectoryIterator($this->_imageDirectory);

        $images = array();
        foreach ($directory as $splFileInfo) {
            if (!$splFileInfo->isDir() && !$splFileInfo->isDot())
                $images[] = $splFileInfo->getRealPath();
        }

        return $images;
    }
}
