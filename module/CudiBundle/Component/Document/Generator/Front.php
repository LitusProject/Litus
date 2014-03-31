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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CudiBundle\Entity\Sale\Article,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Front
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Front extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \CudiBundle\Entity\Sale\Article
     */
    private $_article;

    /**
     * @param \Doctrine\ORM\EntityManager               $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Sale\Article           $article       The article
     * @param \CommonBundle\Component\Util\File\TmpFile $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, Article $article, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/article/front.xsl',
            $file->getFilename()
        );
        $this->_article = $article;
    }

    /**
     * Generate the document.
     *
     * @return void
     */
    public function generate()
    {
        $cachePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.front_page_cache_dir');

        if (!file_exists($cachePath))
            mkdir($cachePath);

        if (null !== $this->_article->getMainArticle()->getFrontPage() && file_exists($cachePath . '/' . $this->_article->getMainArticle()->getFrontPage())) {
            copy($cachePath . '/' . $this->_article->getMainArticle()->getFrontPage(), $this->_pdfPath);
            clearstatcache();
        } else {
            $this->generateXml(
                $this->_xmlFile
            );

            $this->generatePdf();

            do {
                $fileName = sha1(uniqid());
            } while (file_exists($cachePath . '/' . $fileName));

            $this->_article->getMainArticle()->setFrontPage($fileName);
            $this->getEntityManager()->flush();
            copy($this->_pdfPath, $cachePath . '/' . $fileName);
        }
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configuration = $this->getConfigRepository();

        $now = new DateTime();
        $organization_short_name = $configuration->getConfigValue('organization_short_name');
        $organization_name = $configuration->getConfigValue('organization_name');
        $organization_logo = $configuration->getConfigValue('organization_logo');
        $organization_url = $configuration->getConfigValue('organization_url');
        $organization_mail = $configuration->getConfigValue('cudi.mail');
        $university = $configuration->getConfigValue('university');
        $faculty = $configuration->getConfigValue('faculty');
        $address_name = $configuration->getConfigValue('cudi.front_address_name');
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configuration->getConfigValue('cudi.billing_address'));

        $academicYear = $this->_getCurrentAcademicYear();

        $subjects = array();
        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYear($this->_article->getMainArticle(), $academicYear);
        foreach ($mappings as $mapping) {
            $subjects[] = new Object(
                'subject',
                null,
                array(
                    new Object(
                        'code',
                        null,
                        $mapping->getSubject()->getCode()
                    ),
                    new Object(
                        'name',
                        null,
                        $mapping->getSubject()->getName()
                    ),
                )
            );
        }

        if (sizeof($subjects) == 0) {
            $subjects[] = new Object(
                'subject',
                null,
                array(
                    new Object(
                        'code',
                        null,
                        ''
                    ),
                    new Object(
                        'name',
                        null,
                        ''
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'article',
                array(
                    'binding' => $this->_article->getMainArticle()->getBinding()->getCode(),
                ),
                array(
                    new Object(
                        'our_union',
                        array(
                            'short_name' => $organization_short_name
                        ),
                        array(
                            new Object(
                                'name',
                                null,
                                $organization_name
                            ),
                            new Object(
                                'logo',
                                null,
                                $organization_logo
                            )
                        )
                    ),
                    new Object(
                        'academic_year',
                        null,
                        $academicYear->getCode()
                    ),
                    new Object(
                        'university',
                        null,
                        strtoupper($university)
                    ),
                    new Object(
                        'faculty',
                        null,
                        strtoupper($faculty)
                    ),
                    new Object(
                        'address',
                        null,
                        array(
                            new Object(
                                'name',
                                null,
                                $address_name
                            ),
                            new Object(
                                'street',
                                null,
                                $address->getStreet() . ' ' . $address->getNumber() . (null === $address->getMailbox() ? '' : '/' . $address->getMailbox())
                            ),
                            new Object(
                                'city',
                                null,
                                $address->getCountry() . '-' . $address->getPostal() . ' ' . $address->getCity()
                            ),
                            new Object(
                                'site',
                                null,
                                $organization_url
                            ),
                            new Object(
                                'email',
                                null,
                                $organization_mail
                            )
                        )
                    ),
                    new Object(
                        'title',
                        null,
                        $this->_article->getMainArticle()->getTitle()
                    ),
                    new Object(
                        'authors',
                        null,
                        $this->_article->getMainArticle()->getAuthors()
                    ),
                    new Object(
                        'subjects',
                        null,
                        $subjects
                    ),
                    new Object(
                        'price',
                        null,
                        (string) number_format($this->_article->getSellPrice()/100, 2)
                    ),
                    new Object(
                        'barcode',
                        null,
                        substr((string) $this->_article->getBarcode(), 0, 12)
                    ),
                )
            )
        );
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getCurrentAcademicYear()
    {
        return AcademicYear::getOrganizationYear($this->getEntityManager());
    }
}
