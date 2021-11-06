<?php

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Component\Util\Cv as CvUtil;
use BrBundle\Entity\Cv\Entry;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\I18n\Translator;

/**
 * Generates the CV Book for one academic year.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Cv extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Academic      $academic      The academic for which to generate the book.
     * @param TmpFile       $file          The file to write to
     * @param Translator    $translator    The translator
     */
    public function __construct(EntityManager $entityManager, Entry $entry, TmpFile $file, Translator $translator)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/singlecv.xsl',
            $file->getFilename()
        );

        $this->entry = $entry;

        $this->translator = $translator;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'singlecv',
                null,
                CvUtil::getCvXML($this->getEntityManager(), $this->entry, $this->translator)
            )
        );
    }
}
