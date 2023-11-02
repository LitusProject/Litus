<?php

namespace LogisticsBundle\Component\Document\Generator\Pdf;

use BrBundle\Component\Util\Cv;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\I18n\Translator;

/**
 * Generates the requests for coming week.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ComingWeek extends \CommonBundle\Component\Document\Generator\Pdf
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

        $data = Cv::getGrouped($this->getEntityManager(), $this->year);

        $groups = array();

        foreach ($data as $studyData) {
            $groups[] = $this->generateGroup($studyData['name'], $studyData['entries'], $studyData['id']);
        }

        $organization_logo = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_logo');

        $foreword = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.cv_book_foreword');

        $xml->append(
            new Node(
                'request_week',
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
                        'requests',
                        null,
                        $groups
                    ),
                )
            )
        );
    }

    private function generateGroup($groupName, $entries, $id)
    {
        $cvs = array();
        foreach ($entries as $entry) {
            $cvs[] = Cv::getCvXML($this->getEntityManager(), $entry, $this->translator);
        }

        return new Node(
            'cvgroup',
            array(
                'name' => $groupName,
                'id'   => $id,
            ),
            $cvs
        );
    }
}
