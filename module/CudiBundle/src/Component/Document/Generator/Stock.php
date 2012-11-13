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
     * @var \CommonBundle\Entity\General\AcademicYear
     */
    private $_academicYear;

    /**
     * Create a new Article Front Generator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
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
            $filePath . '/stock/stock.xsl',
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
        $union_short_name = $configs->getConfigValue('union_short_name');
        $union_name = $configs->getConfigValue('union_name');
        $logo = $configs->getConfigValue('union_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($configs->getConfigValue('cudi.person'));

        $stock = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findAllByAcademicYear($this->_academicYear);

        $items = array();
        foreach($stock as $item) {
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
                    )
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'stock',
                array(
                    'date' => $now->format('d F Y')
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
                        'items',
                        null,
                        $items
                    ),
                )
            )
        );
    }
}
