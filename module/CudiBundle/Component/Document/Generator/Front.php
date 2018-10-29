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

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use CudiBundle\Entity\Article\Internal as InternalArticle;
use CudiBundle\Entity\Sale\Article;
use Doctrine\ORM\EntityManager;

/**
 * Front
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Front extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Article
     */
    private $article;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Article       $article       The article
     * @param TmpFile       $file          The file to write to
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
        $this->article = $article;
    }

    /**
     * Generate the document.
     *
     * @return void
     */
    public function generate()
    {
        $mainArticle = $this->article->getMainArticle();
        if (!($mainArticle instanceof InternalArticle)) {
            return;
        }

        $cachePath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.front_page_cache_dir');

        if (!file_exists($cachePath)) {
            mkdir($cachePath);
        }

        if ($mainArticle->getFrontPage() !== null && file_exists($cachePath . '/' . $mainArticle->getFrontPage())) {
            copy($cachePath . '/' . $mainArticle->getFrontPage(), $this->pdfPath);
            clearstatcache();
        } else {
            $this->generateXml(
                $this->xmlFile
            );

            $this->generatePdf();

            do {
                $fileName = sha1(uniqid());
            } while (file_exists($cachePath . '/' . $fileName));

            $mainArticle->setFrontPage($fileName);
            $this->getEntityManager()->flush();
            copy($this->pdfPath, $cachePath . '/' . $fileName);
        }
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $mainArticle = $this->article->getMainArticle();
        if (!($mainArticle instanceof InternalArticle)) {
            return;
        }

        $configuration = $this->getConfigRepository();

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

        $academicYear = $this->getCurrentAcademicYear();

        $subjects = array();
        $mappings = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findAllByArticleAndAcademicYear($mainArticle, $academicYear);
        foreach ($mappings as $mapping) {
            $subjects[] = new Node(
                'subject',
                null,
                array(
                    new Node(
                        'code',
                        null,
                        $mapping->getSubject()->getCode()
                    ),
                    new Node(
                        'name',
                        null,
                        $mapping->getSubject()->getName()
                    ),
                )
            );
        }

        if (count($subjects) == 0) {
            $subjects[] = new Node(
                'subject',
                null,
                array(
                    new Node(
                        'code',
                        null,
                        ''
                    ),
                    new Node(
                        'name',
                        null,
                        ''
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'article',
                array(
                    'binding' => $mainArticle->getBinding()->getCode(),
                ),
                array(
                    new Node(
                        'our_union',
                        array(
                            'short_name' => $organization_short_name,
                        ),
                        array(
                            new Node(
                                'name',
                                null,
                                $organization_name
                            ),
                            new Node(
                                'logo',
                                null,
                                $organization_logo
                            ),
                        )
                    ),
                    new Node(
                        'academic_year',
                        null,
                        $academicYear->getCode()
                    ),
                    new Node(
                        'university',
                        null,
                        strtoupper($university)
                    ),
                    new Node(
                        'faculty',
                        null,
                        strtoupper($faculty)
                    ),
                    new Node(
                        'address',
                        null,
                        array(
                            new Node(
                                'name',
                                null,
                                $address_name
                            ),
                            new Node(
                                'street',
                                null,
                                $address->getStreet() . ' ' . $address->getNumber() . ($address->getMailbox() === null ? '' : '/' . $address->getMailbox())
                            ),
                            new Node(
                                'city',
                                null,
                                $address->getCountry() . '-' . $address->getPostal() . ' ' . $address->getCity()
                            ),
                            new Node(
                                'site',
                                null,
                                $organization_url
                            ),
                            new Node(
                                'email',
                                null,
                                $organization_mail
                            ),
                        )
                    ),
                    new Node(
                        'title',
                        null,
                        $mainArticle->getTitle()
                    ),
                    new Node(
                        'authors',
                        null,
                        $mainArticle->getAuthors()
                    ),
                    new Node(
                        'subjects',
                        null,
                        $subjects
                    ),
                    new Node(
                        'price',
                        null,
                        (string) number_format($this->article->getSellPrice() / 100, 2)
                    ),
                    new Node(
                        'barcode',
                        null,
                        substr((string) str_pad($this->article->getBarcode(), 12, '0', STR_PAD_LEFT), 0, 12)
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
    private function getCurrentAcademicYear()
    {
        return AcademicYear::getOrganizationYear($this->getEntityManager());
    }
}
