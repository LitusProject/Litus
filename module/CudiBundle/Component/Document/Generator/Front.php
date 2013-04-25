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

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CudiBundle\Entity\Sales\Article,
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
     * @var \CudiBundle\Entity\Sales\Article
     */
    private $_article;

    /**
     * Create a new Article Front Generator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CudiBundle\Entity\Sales\Article $article The article
     * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
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

            do{
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
     * @param \CommonBundle\Component\Util\TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $now = new DateTime();
        $union_short_name = $configs->getConfigValue('union_short_name');
        $union_name = $configs->getConfigValue('union_name');
        $logo = $configs->getConfigValue('union_logo');
        $union_url = $configs->getConfigValue('union_url');
        $university = $configs->getConfigValue('university');
        $faculty = $configs->getConfigValue('faculty');
        $address_name = $configs->getConfigValue('cudi.front_address_name');
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configs->getConfigValue('cudi.billing_address'));

        $subjects = array();
        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Articles\SubjectMap')
            ->findAllByArticleAndAcademicYear($this->_article->getMainArticle(), $this->_getCurrentAcademicYear());
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
                            'short_name' => $union_short_name
                        ),
                        array(
                            new Object(
                                'name',
                                null,
                                $union_name
                            ),
                            new Object(
                                'logo',
                                null,
                                $logo
                            )
                        )
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
                                $union_url
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
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);
    }
}
