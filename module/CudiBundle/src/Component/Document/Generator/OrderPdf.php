<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CudiBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CudiBundle\Entity\Stock\Orders\Order,
    Doctrine\ORM\EntityManager;

/**
 * OrderPdf
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class OrderPdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \CudiBundle\Entity\Stock\Order
     */
    private $_order;
    
    /**
     * Create a new Order PDF Generator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CudiBundle\Entity\Stock\Order $order The order
     * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
     */
    public function __construct(EntityManager $entityManager, Order $order, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');
                    
           parent::__construct(
               $entityManager,
            $filePath . '/order/order.xsl',
            $file->getFilename()
        );
        $this->_order = $order;
    }
    
    /**
     * Generate the XML for the fop.
     *
     * @param \CommonBundle\Component\Util\TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();
        
        $now = new \DateTime();
        $union_short_name = $configs->getConfigValue('union_short_name');
        $union_name = $configs->getConfigValue('union_name');
        $logo = $configs->getConfigValue('union_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Person')
            ->findOneById($configs->getConfigValue('cudi.person'));

        $delivery_address_name = $configs->getConfigValue('cudi.delivery_address_name');
        $delivery_address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configs->getConfigValue('cudi.delivery_address'));
        $delivery_address_extra = $configs->getConfigValue('cudi.delivery_address_extra');
        $billing_address_name = $configs->getConfigValue('cudi.billing_address_name');
        $billing_address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configs->getConfigValue('cudi.billing_address'));
        
        $external_items = array();
        $internal_items = array();
        foreach($this->_order->getItems() as $item) {
            if ($item->getArticle()->getMainArticle()->isInternal()) {
                $internal_items[] = new Object(
                    'internal_item',
                    null,
                    array(
                        new Object(
                            'barcode',
                            null,
                            (string) $item->getArticle()->getBarcode()
                        ),
                        new Object(
                            'title',
                            null,
                            $item->getArticle()->getMainArticle()->getTitle()
                        ),
                        new Object(
                            'recto_verso',
                            null,
                            $item->getArticle()->getMainArticle()->isRectoVerso() ? '1' : '0'
                        ),
                        new Object(
                            'binding',
                            null,
                            $item->getArticle()->getMainArticle()->getBinding()->getName()
                        ),
                        new Object(
                            'nb_pages',
                            null,
                            (string) ($item->getArticle()->getMainArticle()->getNbBlackAndWhite() + $item->getArticle()->getMainArticle()->getNbColored())
                        ),
                        new Object(
                            'number',
                            null,
                            (string) $item->getNumber()
                        )
                    )
                );
            } else {
                $external_items[] = new Object(
                    'external_item',
                    null,
                    array(
                        new Object(
                            'isbn',
                            null,
                            (string) $item->getArticle()->getBarcode()
                        ),
                        new Object(
                            'title',
                            null,
                            $item->getArticle()->getMainArticle()->getTitle()
                        ),
                        new Object(
                            'author',
                            null,
                            $item->getArticle()->getMainArticle()->getAuthors()
                        ),
                        new Object(
                            'publisher',
                            null,
                            $item->getArticle()->getMainArticle()->getPublishers()
                        ),
                        new Object(
                            'number',
                            null,
                            (string) $item->getNumber()
                        )
                    )
                );
            }
        }
        
        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'order',
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
                             new Object(
                                 'delivery_address',
                                 null,
                                 array(
                                     new Object(
                                         'name',
                                         null,
                                         $delivery_address_name
                                     ),
                                     new Object(
                                         'street',
                                         null,
                                         $delivery_address->getStreet() . ' ' . $delivery_address->getNumber()
                                     ),
                                     new Object(
                                         'city',
                                         null,
                                         $delivery_address->getPostal() . ' ' . $delivery_address->getCity()
                                     ),
                                     new Object(
                                         'extra',
                                         null,
                                         $delivery_address_extra
                                     )
                                 )
                             ),
                             new Object(
                                 'billing_address',
                                 null,
                                 array(
                                     new Object(
                                         'name',
                                         null,
                                         $billing_address_name
                                     ),
                                     new Object(
                                         'person',
                                         null,
                                         $person->getFullname()
                                     ),
                                     new Object(
                                         'street',
                                         null,
                                         $billing_address->getStreet() . ' ' . $billing_address->getNumber()
                                     ),
                                     new Object(
                                         'city',
                                         null,
                                         $billing_address->getPostal() . ' ' . $billing_address->getCity()
                                     )
                                 )
                             )
                        )
                    ),
                    new Object(
                        'external_items',
                        null,
                        $external_items
                    ),
                    new Object(
                        'internal_items',
                        null,
                        $internal_items
                    )
                )
            )
        );
    }
}
