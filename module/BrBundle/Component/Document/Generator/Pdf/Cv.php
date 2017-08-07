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

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Cv\Entry,
    BrBundle\Entity\Cv\Util,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Zend\Mvc\I18n\Translator;

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
     * @param AcademicYear  $year          The academic year for which to generate the book.
     * @param Academic      $academic      The academic for which to generate the book.
     * @param TmpFile       $file          The file to write to
     * @param Translator    $translator    The translator
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, Entry $entry, TmpFile $file, Translator $translator)
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
            new Object(
                'singlecv',
                null,
                Util::getCvXML($this->getEntityManager(), $this->entry, $this->translator)
            )
        );
    }
}
