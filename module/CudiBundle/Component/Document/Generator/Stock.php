<?php

namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use CommonBundle\Entity\General\AcademicYear;
use DateTime;
use Doctrine\ORM\EntityManager;

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
    private $articles;

    /**
     * @var string
     */
    private $order;

    /**
     * @var boolean
     */
    private $onlyInStock;

    /**
     * @var AcademicYear
     */
    private $academicYear;

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

        $this->articles = $articles;
        $this->order = $order;
        $this->onlyInStock = $onlyInStock;
        $this->academicYear = $academicYear;
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

        if ($this->order == 'barcode') {
            $stock = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYearSortBarcode($this->academicYear);
        } else {
            $stock = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findAllByAcademicYear($this->academicYear);
        }

        $items = array();
        foreach ($stock as $item) {
            if ($this->articles == 'external' && $item->getMainArticle()->isInternal()) {
                continue;
            }
            if ($this->articles == 'internal' && !$item->getMainArticle()->isInternal()) {
                continue;
            }

            if ($item->getStockValue() <= 0 && $this->onlyInStock) {
                continue;
            }

            $items[] = new Node(
                'item',
                null,
                array(
                    new Node(
                        'barcode',
                        null,
                        (string) $item->getBarcode()
                    ),
                    new Node(
                        'title',
                        null,
                        $item->getMainArticle()->getTitle()
                    ),
                    new Node(
                        'author',
                        null,
                        $item->getMainArticle()->getAuthors()
                    ),
                    new Node(
                        'publisher',
                        null,
                        $item->getMainArticle()->getPublishers()
                    ),
                    new Node(
                        'amount',
                        null,
                        (string) $item->getStockValue()
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'stock',
                array(
                    'date' => $now->format('d F Y'),
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
                        'cudi',
                        array(
                            'name' => $cudi_name,
                        ),
                        array(
                            new Node(
                                'mail',
                                null,
                                $cudi_mail
                            ),
                            new Node(
                                'phone',
                                null,
                                $person->getPhoneNumber()
                            ),
                        )
                    ),
                    new Node(
                        'items',
                        null,
                        $items
                    ),
                )
            )
        );
    }
}
