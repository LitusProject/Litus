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
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Financial
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Financial extends \CommonBundle\Component\Document\Generator\Pdf
{

    /**
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_academicYear;

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

        $this->_academicYear = $academicYear;
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
        $organization_short_name = $configs->getConfigValue('organization_short_name');
        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($configs->getConfigValue('cudi.person'));

        $period = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();
        $period->setEntityManager($this->getEntityManager());

        $articles = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findAllByAcademicYear($this->_academicYear);

        $external = array();
        $internal = array();

        foreach($articles as $article) {
            $virtualOrdered = $period->getNbVirtualOrdered($article);

            $object = new Object(
                'item',
                null,
                array(
                    new Object(
                        'barcode',
                        null,
                        (string) $article->getBarcode()
                    ),
                    new Object(
                        'title',
                        null,
                        $article->getMainArticle()->getTitle()
                    ),
                    new Object(
                        'author',
                        null,
                        $article->getMainArticle()->getAuthors()
                    ),
                    new Object(
                        'publisher',
                        null,
                        $article->getMainArticle()->getPublishers()
                    ),
                    new Object(
                        'ordered',
                        null,
                        (string) $period->getNbOrdered($article) . ($virtualOrdered > 0 ? '(+ ' . $virtualOrdered . ')' : '')
                    ),
                    new Object(
                        'delivered',
                        null,
                        (string) $period->getNbDelivered($article)
                    ),
                    new Object(
                        'sold',
                        null,
                        (string) $period->getNbSold($article)
                    ),
                    new Object(
                        'stock',
                        null,
                        (string) $article->getStockValue()
                    ),
                )
            );

            if ($article->getMainArticle()->isInternal())
                $internal[] = $object;
            else
                $external[] = $object;
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'financial',
                array(
                    'date' => $now->format('d F Y')
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
                        'cudi',
                        array(
                            'name' => $cudi_name
                        ),
                        array(
                             new Object(
                                 'mail',
                                 null,
                                 $cudi_mail
                             ),
                             new Object(
                                 'phone',
                                 null,
                                 $person->getPhoneNumber()
                             ),
                        )
                    ),
                    new Object(
                        'external_items',
                        null,
                        $external
                    ),
                    new Object(
                        'internal_items',
                        null,
                        $internal
                    ),
                )
            )
        );
    }
}