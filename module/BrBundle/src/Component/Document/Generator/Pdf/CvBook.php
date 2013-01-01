<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace BrBundle\Component\Document\Pdf;

use BrBundle\Entity\Cv,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager;

/**
 * Generates the CV Book for one academic year.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CvBook extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_year;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\AcademicYear $year The academic year for which to generate the book.
     */
    public function __construct(EntityManager $entityManager, AcademicYear $year, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/cv/cvbook.xsl',
            $file->getFilename()
        );

        $this->_entityManager = $entityManager;
        $this->_year = $year;
    }

    protected function generateXml(TmpFile $tmpFile)
    {
        $xml = new Generator($tmpFile);

        // Generate the xml
        $entries = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllByAcademicYear($this->_year);

        // TODO: this is dummy xml
        $xml->append(
            new Object(
                'xmlname',
                array(
                    'paramname' => 'paramvalue',
                ),
                array(
                    new Object(
                        'child',
                        array(
                            'childparam' => 'value',
                        ),
                        null
                    )
                )
            )
        );
    }
}
