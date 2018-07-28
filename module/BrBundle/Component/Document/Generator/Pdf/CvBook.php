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
    CommonBundle\Component\Util\Xml\Node,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Zend\Mvc\I18n\Translator;

/**
 * Generates the CV Book for one academic year.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvBook extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var AcademicYear
     */
    private $year;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param AcademicYear  $year          The academic year for which to generate the book.
     * @param TmpFile       $file          The file to write to
     * @param Translator    $translator    The translator
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, TmpFile $file, Translator $translator)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/cvbook.xsl',
            $file->getFilename()
        );

        $this->year = $year;
        $this->translator = $translator;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        $data = Util::getGrouped($this->getEntityManager(), $this->year);

        $groups = array();

        foreach ($data as $studyData) {
            $groups[] = $this->generateGroup($studyData['name'], $studyData['entries']);
        }

        $organization_logo = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_logo');

        $foreword = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_foreword');

        $xml->append(
            new Node(
                'cvbook',
                array(
                    'logo'  => $organization_logo,
                    'index' => $this->translator->translate('Alphabetical Index'),
                    'toc'   => $this->translator->translate('Table of Contents'),
                ),
                array(
                    new Node(
                        'foreword',
                        array(
                            'title' => $this->translator->translate('Foreword'),
                        ),
                        Node::fromString($foreword)
                    ),
                    new Node(
                        'cvs',
                        null,
                        $groups
                    ),
                )
            )
        );
    }

    private function generateGroup($groupName, $entries)
    {
        $cvs = array();
        foreach ($entries as $entry) {
            $cvs[] = Util::getCvXML($this->getEntityManager(), $entry, $this->translator);
        }

        return new Node(
            'cvgroup',
            array(
                'name' => $groupName,
            ),
            $cvs
        );
    }
}
