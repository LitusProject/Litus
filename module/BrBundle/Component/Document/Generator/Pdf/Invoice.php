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

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Invoice as InvoiceEntity,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator as XmlGenerator,
    CommonBundle\Component\Util\Xml\Object as XmlObject,
    Doctrine\ORM\EntityManager;

/**
 * Generate a PDF for an invoice.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Invoice extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \BrBundle\Entity\Invoice
     */
    private $_invoice;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Invoice    $invoice       The invoice for which we want to generate a PDF
     */
    public function __construct(EntityManager $entityManager, InvoiceEntity $invoice)
    {
        parent::__construct(
            $entityManager,
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . '/invoice/invoice.xsl',
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/contracts/'
                . $invoice->getOrder()->getContract()->getId() . '/invoice.pdf'
        );
        $this->_invoice = $invoice;
    }

    protected function generateXml(TmpFile $file)
    {
        $xml = new XmlGenerator($file);

        $configs = $this->getConfigRepository();

        $totalExclusive = 0;
        $totalVat = 0;

        $invoiceDate = $this->_invoice->getCreationTime()->format('j/m/Y');
        $dueDate = $this->_invoice->getExpirationTime($this->getEntityManager())->format('j/m/Y');
        $clientVat = $this->_VATFormat($this->_invoice->getOrder()->getCompany()->getVatNumber());
        $reference = '/'; // TODO? (this was here already)

        $invoiceNb = $this->_invoice->getInvoiceNumber($this->getEntityManager());

        $unionName = $configs->getConfigValue('br.organization_name');
        $unionAddressArray = unserialize($configs->getConfigValue('organization_address_array'));
        $logo = $configs->getConfigValue('organization_logo');
        $unionVat = $configs->getConfigValue('br.vat_number');

        if ('' == $this->_invoice->getVATContext()) {
            $vatTypeExplanation = "";
        } else {
            $vatTypeExplanation = $configs->getConfigValue('br.invoice_vat_explanation') . " " . $this->_invoice->getVATContext();
        }

        $subEntries = unserialize($configs->getConfigValue('br.invoice_below_entries'))['nl'];

        $vatTypes = unserialize($configs->getConfigValue('br.vat_types'));

        $company = $this->_invoice->getOrder()->getCompany();
        $companyContactPerson = $this->_invoice->getOrder()->getContact()->getFullName();
        $companyName = $company->getName();

        $count = 0;
        $entries = array();
        foreach ($this->_invoice->getEntries() as $entry) {
            $product = $entry->getOrderEntry()->getProduct();
            $price = $product->getPrice() / 100;

            if (($price > 0) || (null !== $entry->getInvoiceDescription() && '' != $entry->getInvoiceDescription())) {
                $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,
                            (null === $entry->getInvoiceDescription() || '' == $entry->getInvoiceDescription())
                                    ? $product->getName()
                                    : $entry->getInvoiceDescription()
                        ),
                        new XmlObject('price', null, XmlObject::fromString('<euro/>' . number_format($price, 2))),
                        new XmlObject('amount', null, $entry->getOrderEntry()->getQuantity() . ''),
                        new XmlObject('vat_type', null, $vatTypes[$product->getVatType()] . '%'),
                    )
                );

                $totalExclusive += $price * $entry->getOrderEntry()->getQuantity();
                $totalVat += ($price * $entry->getOrderEntry()->getQuantity() * $vatTypes[$product->getVatType()]) / 100;

                $count++;
            }
        }

        while ($count < 8) {
            $entries[] = new XmlObject('empty_line');
            $count++;
        }

        $entries[] = new XmlObject('empty_line');
        $entries[] = new XmlObject('empty_line');

        $discount = $this->_invoice->getOrder()->getContract()->getDiscount();
        if (0 != $discount) {
            if ('' == $this->_invoice->getOrder()->getContract()->getDiscountContext()) {
                $entries[] = new XmlObject('entry', null,
                array(
                    new XmlObject('description', null,"Korting"),
                    new XmlObject('price', null, XmlObject::fromString('- <euro/>' . number_format($discount, 2))),
                    new XmlObject('amount', null, ' '),
                    new XmlObject('vat_type', null, ' '),
                )
            );
            } else {
                $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,$this->_invoice->getOrder()->getContract()->getDiscountContext()),
                        new XmlObject('price', null, XmlObject::fromString('- <euro/>' . number_format($discount, 2))),
                        new XmlObject('amount', null, ' '),
                        new XmlObject('vat_type', null, ' '),
                    )
                );
            }
        }

        $totalExclusive = $totalExclusive - $discount;

        $total = $totalExclusive + $totalVat;

        $xml->append(new XmlObject('invoice', null,
            array(
                new XmlObject('title', null,
                    array(
                        new XmlObject('invoice_number', null, $invoiceNb),
                        new XmlObject('invoice_date', null, $invoiceDate),
                        new XmlObject('expiration_date', null, $dueDate),
                        new XmlObject('vat_client', null, $clientVat),
                        new XmlObject('reference', null, $reference),
                    )
                ),

                new XmlObject('our_union', null,
                    array(
                        new XmlObject('name', null, $unionName),
                        new XmlObject(
                            'address',
                            null,
                            array(
                                new XmlObject(
                                    'street',
                                    null,
                                    $unionAddressArray['street']
                                ),
                                new XmlObject(
                                    'number',
                                    null,
                                    $unionAddressArray['number']
                                ),
                                new XmlObject(
                                    'mailbox',
                                    null,
                                    $unionAddressArray['mailbox']
                                ),
                                new XmlObject(
                                    'postal',
                                    null,
                                    $unionAddressArray['postal']
                                ),
                                new XmlObject(
                                    'city',
                                    null,
                                    $unionAddressArray['city']
                                ),
                                new XmlObject(
                                    'country',
                                    null,
                                    $unionAddressArray['country']
                                ),
                            )
                        ),
                        new XmlObject('logo', null, $logo),
                        new XmlObject('vat_number', null, $unionVat),
                    )
                ),

                new XmlObject('company', array('contact_person' => $companyContactPerson),
                    array(
                        new XmlObject('name', null, $companyName),
                        new XmlObject(
                            'address',
                            null,
                            array(
                                new XmlObject(
                                    'street',
                                    null,
                                    $company->getAddress()->getStreet()
                                ),
                                new XmlObject(
                                    'number',
                                    null,
                                    $company->getAddress()->getNumber()
                                ),
                                new XmlObject(
                                    'mailbox',
                                    null,
                                    $company->getAddress()->getMailbox()
                                ),
                                new XmlObject(
                                    'postal',
                                    null,
                                    $company->getAddress()->getPostal()
                                ),
                                new XmlObject(
                                    'city',
                                    null,
                                    $company->getAddress()->getCity()
                                ),
                                new XmlObject(
                                    'country',
                                    null,
                                    $company->getAddress()->getCountry()
                                ),
                            )
                        ),
                    )
                ),

                new XmlObject('entries', null, $entries),

                new XmlObject('total', null,
                    array(
                        new XmlObject('vat_type_explanation', null, $vatTypeExplanation),
                        new XmlObject('price_excl', null, XmlObject::fromString('<euro/>' . number_format($totalExclusive, 2))),
                        new XmlObject('price_vat', null, XmlObject::fromString('<euro/>' . number_format($totalVat, 2))),
                        new XmlObject('price_incl', null, XmlObject::fromString('<euro/>' . number_format($total, 2))),
                    )
                ),

                new XmlObject('sub_entries', null, $subEntries),

                new XmlObject('footer'),

                new XmlObject('sale_conditions_nl'),
            )
        ));
    }

    private function _VATFormat($vat)
    {
        return substr_replace(substr_replace(substr_replace($vat, " ", 2,0),".",7,0),".",11,0);
    }
}
