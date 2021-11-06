<?php

namespace CudiBundle\Component\Document\Generator\Order;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use CudiBundle\Entity\Stock\Order;
use Doctrine\ORM\EntityManager;

/**
 * OrderPdf
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Pdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var string
     */
    private $sortOrder;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Order         $order         The order
     * @param string        $sortOrder
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, Order $order, $sortOrder, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/order/templates/' . $order->getSupplier()->getTemplate() . '.xsl',
            $file->getFilename()
        );
        $this->order = $order;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $organization_short_name = $configs->getConfigValue('organization_short_name');
        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');
        $cudi_name = $configs->getConfigValue('cudi.name');
        $cudi_mail = $configs->getConfigValue('cudi.mail');
        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($configs->getConfigValue('cudi.person'));

        $delivery_address_name = $configs->getConfigValue('cudi.delivery_address_name');
        $delivery_address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configs->getConfigValue('cudi.delivery_address'));
        $delivery_address_extra = $configs->getConfigValue('cudi.delivery_address_extra');
        $billing_address_name = $configs->getConfigValue('cudi.billing_address_name');
        $billing_address_VAT = $configs->getConfigValue('cudi.billing_address_VAT');
        $billing_address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Address')
            ->findOneById($configs->getConfigValue('cudi.billing_address'));

        $external_items = array();
        $internal_items = array();

        if ($this->sortOrder == 'barcode') {
            $items = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderOnBarcode($this->order);
        } else {
            $items = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderOnAlpha($this->order);
        }

        foreach ($items as $item) {
            if ($item->getArticle()->getMainArticle()->isInternal()) {
                $internal_items[] = new Node(
                    'internal_item',
                    null,
                    array(
                        new Node(
                            'barcode',
                            null,
                            (string) $item->getArticle()->getBarcode()
                        ),
                        new Node(
                            'title',
                            null,
                            $item->getArticle()->getMainArticle()->getTitle()
                        ),
                        new Node(
                            'recto_verso',
                            null,
                            $item->getArticle()->getMainArticle()->isRectoVerso() ? '1' : '0'
                        ),
                        new Node(
                            'colored',
                            null,
                            $item->getArticle()->getMainArticle()->isColored() ? '1' : '0'
                        ),
                        new Node(
                            'binding',
                            null,
                            $item->getArticle()->getMainArticle()->getBinding()->getName()
                        ),
                        new Node(
                            'nb_pages',
                            null,
                            (string) ($item->getArticle()->getMainArticle()->getNbBlackAndWhite() + $item->getArticle()->getMainArticle()->getNbColored())
                        ),
                        new Node(
                            'amount',
                            null,
                            (string) $item->getNumber()
                        ),
                    )
                );
            } else {
                $external_items[] = new Node(
                    'external_item',
                    null,
                    array(
                        new Node(
                            'isbn',
                            null,
                            (string) $item->getArticle()->getMainArticle()->getIsbn()
                        ),
                        new Node(
                            'title',
                            null,
                            $item->getArticle()->getMainArticle()->getTitle()
                        ),
                        new Node(
                            'author',
                            null,
                            $item->getArticle()->getMainArticle()->getAuthors()
                        ),
                        new Node(
                            'publisher',
                            null,
                            $item->getArticle()->getMainArticle()->getPublishers()
                        ),
                        new Node(
                            'amount',
                            null,
                            (string) $item->getNumber()
                        ),
                    )
                );
            }
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'order',
                array(
                    'date' => $this->order->getDateOrdered()->format('d F Y'),
                ),
                array(
                    new Node(
                        'comment',
                        array(),
                        $this->order->getComment()
                    ),
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
                            new Node(
                                'delivery_address',
                                null,
                                array(
                                    new Node(
                                        'name',
                                        null,
                                        $delivery_address_name
                                    ),
                                    new Node(
                                        'street',
                                        null,
                                        $delivery_address->getStreet() . ' ' . $delivery_address->getNumber() . ($delivery_address->getMailbox() === null ? '' : '/' . $delivery_address->getMailbox())
                                    ),
                                    new Node(
                                        'city',
                                        null,
                                        $delivery_address->getPostal() . ' ' . $delivery_address->getCity()
                                    ),
                                    new Node(
                                        'extra',
                                        null,
                                        $delivery_address_extra
                                    ),
                                )
                            ),
                            new Node(
                                'billing_address',
                                null,
                                array(
                                    new Node(
                                        'name',
                                        null,
                                        $billing_address_name
                                    ),
                                    new Node(
                                        'VAT',
                                        null,
                                        $billing_address_VAT
                                    ),
                                    new Node(
                                        'person',
                                        null,
                                        $person->getFullname()
                                    ),
                                    new Node(
                                        'street',
                                        null,
                                        $billing_address->getStreet() . ' ' . $billing_address->getNumber() . ($billing_address->getMailbox() === null ? '' : '/' . $billing_address->getMailbox())
                                    ),
                                    new Node(
                                        'city',
                                        null,
                                        $billing_address->getPostal() . ' ' . $billing_address->getCity()
                                    ),
                                )
                            ),
                        )
                    ),
                    new Node(
                        'external_items',
                        null,
                        $external_items
                    ),
                    new Node(
                        'internal_items',
                        null,
                        $internal_items
                    ),
                )
            )
        );
    }
}
