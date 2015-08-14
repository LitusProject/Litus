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
    private $invoide;

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
        $this->invoide = $invoice;
    }

    protected function generateXml(TmpFile $file)
    {
        $xml = new XmlGenerator($file);

        $configs = $this->getConfigRepository();

        $totalExclusive = 0;
        $totalVat = 0;

        $invoiceDate = $this->invoide->getCreationTime()->format('j/m/Y');
        $dueDate = $this->invoide->getExpirationTime($this->getEntityManager())->format('j/m/Y');
        $paymentDays = $this->invoide->getOrder()->getContract()->getPaymentDays();
        $clientVat = $this->vatFormat($this->invoide->getOrder()->getCompany()->getInvoiceVatNumber());
        $reference =  $this->invoide->getCompanyReference();

        $invoiceNb = $this->invoide->getInvoiceNumber($this->getEntityManager());

        $unionName = $configs->getConfigValue('br.organization_name');
        $unionAddressArray = unserialize($configs->getConfigValue('organization_address_array'));
        $logo = $configs->getConfigValue('organization_logo');
        $unionVat = $configs->getConfigValue('br.vat_number');

        if ('' == $this->invoide->getVatContext()) {
            $vatTypeExplanation = '';
        } else {
            $vatTypeExplanation = $configs->getConfigValue('br.invoice_vat_explanation') . ' ' . $this->invoide->getVatContext();
        }

        $subEntries = unserialize($configs->getConfigValue('br.invoice_below_entries'))['nl'];

        $vatTypes = unserialize($configs->getConfigValue('br.vat_types'));

        $company = $this->invoide->getOrder()->getCompany();
        $companyContactPerson = $this->invoide->getOrder()->getContact()->getFullName();
        $companyName = $company->getInvoiceName();

        $count = 0;
        $entries = array();
        foreach ($this->invoide->getEntries() as $entry) {
            $product = $entry->getOrderEntry()->getProduct();
            $price = $product->getPrice() / 100;

            if (($price > 0) || (null !== $entry->getInvoiceDescription() && '' != $entry->getInvoiceDescription())) {
                $tax = $this->invoide->getTaxFree() ? 0 : $vatTypes[$product->getVatType()];

                $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,
                            (null === $entry->getInvoiceDescription() || '' == $entry->getInvoiceDescription())
                                    ? $product->getName()
                                    : $entry->getInvoiceDescription()
                        ),
                        new XmlObject('price', null, XmlObject::fromString('<euro/> ' . number_format($price, 2))),
                        new XmlObject('amount', null, $entry->getOrderEntry()->getQuantity() . ''),
                        new XmlObject('vat_type', null,  $tax . '%'),
                    )
                );

                $totalExclusive += $price * $entry->getOrderEntry()->getQuantity();

                if (!$this->invoide->getTaxFree()) {
                    $totalVat += ($price * $entry->getOrderEntry()->getQuantity() * $vatTypes[$product->getVatType()]) / 100;
                }

                $count++;
            }
        }

        $percentage = $this->invoide->getOrder()->getAutoDiscountPercentage()/100;
        $autoDiscount = -$totalExclusive * $percentage;

        $discountTax = $this->invoide->getTaxFree() ? 0 : 21;

        if ( $percentage > 0) {
            $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,
                            (null === $this->invoide->getAutoDiscountText() || '' == $this->invoide->getAutoDiscountText())
                                    ? 'Korting: ' . $percentage . '%'
                                    : $this->invoide->getAutoDiscountText()
                        ),
                        new XmlObject('price', null, XmlObject::fromString('<euro/> ' . number_format($autoDiscount, 2))),
                        new XmlObject('amount', null, '1'),
                        new XmlObject('vat_type', null,  $discountTax . '%'),
                    )
                );

            $totalVat += $autoDiscount * $discountTax/100;

            $count++;
        }

        while ($count < 8) {
            $entries[] = new XmlObject('empty_line');
            $count++;
        }

        $entries[] = new XmlObject('empty_line');
        $entries[] = new XmlObject('empty_line');

        $discount = $this->invoide->getOrder()->getDiscount();
        if (0 != $discount) {
            if ('' == $this->invoide->getDiscountText()) {
                $entries[] = new XmlObject('entry', null,
                array(
                    new XmlObject('description', null, 'Korting'),
                    new XmlObject('price', null, XmlObject::fromString('<euro/> -' . number_format($discount, 2))),
                    new XmlObject('amount', null, '1'),
                    new XmlObject('vat_type', null, $discountTax . '%'),
                )
            );
            } else {
                $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,$this->invoide->getDiscountText()),
                        new XmlObject('price', null, XmlObject::fromString('<euro/> -' . number_format($discount, 2))),
                        new XmlObject('amount', null, '1'),
                        new XmlObject('vat_type', null, $discountTax . '%'),
                    )
                );
            }

            $totalVat += -$discount * $discountTax/100;
        }

        $totalExclusive = $totalExclusive - $discount + $autoDiscount;

        $total = $totalExclusive + $totalVat;

        $xml->append(new XmlObject('invoice',
            array(
                'payment_days' => (String) $paymentDays,
                ),
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
                                    $company->getInvoiceAddress()->getStreet()
                                ),
                                new XmlObject(
                                    'number',
                                    null,
                                    $company->getInvoiceAddress()->getNumber()
                                ),
                                new XmlObject(
                                    'mailbox',
                                    null,
                                    $company->getInvoiceAddress()->getMailbox()
                                ),
                                new XmlObject(
                                    'postal',
                                    null,
                                    $company->getInvoiceAddress()->getPostal()
                                ),
                                new XmlObject(
                                    'city',
                                    null,
                                    $company->getInvoiceAddress()->getCity()
                                ),
                                new XmlObject(
                                    'country',
                                    null,
                                    $company->getInvoiceAddress()->getCountry()
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

    /**
     * @param  string $vat
     * @return string
     */
    private function vatFormat($vat)
    {
        return substr_replace(substr_replace(substr_replace($vat, ' ', 2, 0), '.', 7, 0), '.', 11, 0);
    }
}
