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

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager;

/**
 * Financial
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Financial extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $articles The kind of articles to export
     * @param string $order The ordering of the articles to export
     * @param boolean $onlyInStock Print only articles in stock
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
     */
    public function __construct(EntityManager $entityManager, AcademicYear $academicYear, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/financial/articles.xsl',
            $file->getFilename()
        );
    }

    /**
     * Generate the XML for the fop.
     *
     * @param \CommonBundle\Component\Util\TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {

    }
}