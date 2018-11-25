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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\PassKit;

use CommonBundle\Component\Util\File\TmpFile;
use DirectoryIterator;
use ZipArchive;

/**
 * This class can be used to generate Apple Pass Kit passes.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Pass
{
    /**
     * @var TmpFile The temporary file for the pass
     */
    private $pass = null;

    /**
     * @var string The location of the image directory
     */
    private $imageDirectory = '';

    /**
     * @var TmpFile The temporary file for the manifest
     */
    private $manifest = null;

    /**
     * @var TmpFile The temporary file for the signature
     */
    private $signature = null;

    /**
     * @var string The pass' serial number
     */
    private $serialNumber = '';

    /**
     * @var array The pass' languages
     */
    private $languages = array();

    /**
     * @param TmpFile $pass           The temporary file for the pass
     * @param string  $imageDirectory The location of the image directory
     */
    public function __construct(TmpFile $pass, $imageDirectory)
    {
        $this->pass = $pass;
        $this->imageDirectory = $imageDirectory;

        $this->manifest = new TmpFile();
        $this->signature = new TmpFile();
    }

    /**
     * Creates the pass archive.
     *
     * @return void
     */
    public function createPass()
    {
        $this->createManifest();

        $pass = new ZipArchive();
        $pass->open($this->pass->getFilename(), ZipArchive::CREATE);
        $pass->addFromString('signature', $this->createSignature());
        $pass->addFromString('manifest.json', $this->manifest->getContent());
        $pass->addFromString('pass.json', $this->getJson());

        foreach ($this->getImages() as $image) {
            $pass->addFile($image, basename($image));
        }

        $languages = array_keys($this->languages);
        for ($i = 0; isset($languages[$i]); $i++) {
            $pass->addEmptyDir($languages[$i] . '.lproj');
            $pass->addFromString($languages[$i] . '.lproj/pass.strings', $this->createLanguage($languages[$i]));
        }

        $pass->close();
    }

    /**
     * Add a new language to the pass.
     *
     * @param string $name    The name of the language
     * @param array  $strings The localised strings
     */
    protected function addLanguage($name, array $strings)
    {
        $this->languages[$name] = $strings;

        return $this;
    }

    /**
     * Get the certicicate used to sign the pass.
     *
     * @return array
     */
    abstract protected function getCertificate();

    /**
     * Get the pass' JSON directory.
     *
     * @return string
     */
    abstract protected function getJson();

    /**
     * Generates the pass' serial number (or generates a new one)
     *
     * @return string
     */
    protected function getSerialNumber()
    {
        if ($this->serialNumber == '') {
            $this->serialNumber = uniqid();
        }

        return $this->serialNumber;
    }

    /**
     * Create the pass' manifest.
     *
     * @return void
     */
    private function createManifest()
    {
        $hashes = array(
            'pass.json' => sha1($this->getJson()),
        );

        foreach ($this->getImages() as $image) {
            $hashes[strtolower(basename($image))] = sha1(file_get_contents($image));
        }

        $languages = array_keys($this->languages);
        for ($i = 0; isset($languages[$i]); $i++) {
            $hashes[$languages[$i] . '.lproj/pass.strings'] = sha1($this->createLanguage($languages[$i]));
        }

        $this->manifest->appendContent(json_encode($hashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Create the pass' signature.
     *
     * @return string
     */
    private function createSignature()
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
            $this->manifest->getFilename(),
            $this->signature->getFilename(),
            $certificateData,
            $privateKey,
            array(),
            PKCS7_BINARY | PKCS7_DETACHED,
            'data/certificates/AppleWWDRCA.pem'
        );

        return $this->convertPemToDer($this->signature->getContent());
    }

    private function createLanguage($name)
    {
        $strings = '';
        foreach ($this->languages[$name] as $key => $value) {
            $strings .= '"' . $key . '" = "' . $value . '";' . PHP_EOL;
        }

        return $strings;
    }

    /**
     * Convert a certifcate in the PEM format to a DER.
     *
     * @param  string $signature The PEM signature
     * @return string
     */
    private function convertPemToDer($signature)
    {
        $signature = substr($signature, (strpos($signature, 'filename="smime.p7s"') + 20));

        return base64_decode(trim(substr($signature, 0, strpos($signature, '------'))));
    }

    /**
     * Retrieves a list of all the files in the image directory.
     *
     * @return array
     */
    private function getImages()
    {
        $directory = new DirectoryIterator($this->imageDirectory);

        $images = array();
        foreach ($directory as $splFileInfo) {
            if (!$splFileInfo->isDir() && !$splFileInfo->isDot()) {
                $images[] = $splFileInfo->getRealPath();
            }
        }

        return $images;
    }
}
