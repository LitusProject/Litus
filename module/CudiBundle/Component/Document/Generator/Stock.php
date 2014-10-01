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

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CommonBundle\Entity\General\AcademicYear,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Stock extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var string
     */
    private $_articles;

    /**
     * @var string
     */
    private $_order;

    /**
     * @var boolean
     */
    private $_onlyInStock;

    /**
     * @var AcademicYear
     */
    private $_academicYear;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param string        $articles      The kind of articles to export
     * @param string        $order         The ordering of the articles to export
     * @param boolean       $onlyInStock   Print only articles in stock
     * @param AcademicYear  $academicYear
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, $articles, $order, $onlyInStock, AcademicYear $academicYear, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/stock/stock.xsl',
            $file->getFilename()
        );

        $this->_articles = $articles;
        $this->_order = $order;
        $this->_onlyInStock = $onlyInStock;
        $this->_academicYear = $academicYear;
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
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

        if ($this->_order == 'barcode') {
            $stock = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYearSortBarcode($this->_academicYear);
        } else {
            $stock = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYear($this->_academicYear);
        }

        $items = array();
        foreach ($stock as $item) {
            if ($this->_articles == 'external' && $item->getMainArticle()->isInternal()) {
                continue;
            }
            if ($this->_articles == 'internal' && !$item->getMainArticle()->isInternal()) {
                continue;
            }

            if ($item->getStockValue() <= 0 && $this->_onlyInStock) {
                continue;
            }

            $items[] = new Object(
                'item',
                null,
                array(
                    new Object(
                        'barcode',
                        null,
                        (string) $item->getBarcode()
                    ),
                    new Object(
                        'title',
                        null,
                        $item->getMainArticle()->getTitle()
                    ),
                    new Object(
                        'author',
                        null,
                        $item->getMainArticle()->getAuthors()
                    ),
                    new Object(
                        'publisher',
                        null,
                        $item->getMainArticle()->getPublishers()
                    ),
                    new Object(
                        'amount',
                        null,
                        (string) $item->getStockValue()
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'stock',
                array(
                    'date' => $now->format('d F Y'),
                ),
                array(
                    new Object(
                        'our_union',
                        array(
                            'short_name' => $organization_short_name,
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
                            ),
                        )
                    ),
                    new Object(
                        'cudi',
                        array(
                            'name' => $cudi_name,
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
                        'items',
                        null,
                        $items
                    ),
                )
            )
        );
    }
}
