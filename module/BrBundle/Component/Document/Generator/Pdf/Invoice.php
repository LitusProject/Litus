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

namespace BrBundle\Component\Document\Generator\Pdf;

use BrBundle\Entity\Invoice\ContractInvoice as InvoiceEntity;
use CommonBundle\Component\Document\Generator\Pdf as PdfGenerator;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator as XmlGenerator;
use CommonBundle\Component\Util\Xml\Node as XmlNode;
use Doctrine\ORM\EntityManager;

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
     * @var string The language used for the invoice
     */
    private $lang = 'nl';

    /**
     * @var String The xsl file path, relative to the br generator path, to use for each language
     */
    const INVOICE_XSL_PATHS = array(
        null                  => '/invoice/invoice_default.xsl',
        PdfGenerator::ENGLISH => '/invoice/invoice_en.xsl',
        PdfGenerator::DUTCH   => '/invoice/invoice_nl.xsl',
    );

    /**
     * @param \Doctrine\ORM\EntityManager              $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Invoice\ContractInvoice $invoice       The invoice for which we want to generate a PDF
     * @param string                                   $language      The language we want to generate the PDF in
     */
    public function __construct(EntityManager $entityManager, InvoiceEntity $invoice, String $language = null)
    {
        parent::__construct(
            $entityManager,
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.pdf_generator_path') . Invoice::INVOICE_XSL_PATHS[$language],
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.file_path') . '/invoices/'
                . $invoice->getInvoiceNumberPrefix() . '/'
                . $invoice->getInvoiceNumber() . '.pdf'
        );
        $this->invoide = $invoice;
        if ($language !== null) {
            $this->lang = $language;
        }
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
        $clientVat = $this->invoide->getOrder()->getCompany()->getInvoiceVatNumber();
        $reference = $this->invoide->getCompanyReference();

        $invoiceNb = $this->invoide->getInvoiceNumber();

        $unionName = $configs->getConfigValue('br.organization_name');
        $unionAddressArray = unserialize($configs->getConfigValue('organization_address_array'));
        $logo = $configs->getConfigValue('organization_logo');
        $unionVat = $configs->getConfigValue('br.vat_number');

        if ($this->invoide->getVatContext() == '') {
            $vatTypeExplanation = '';
        } else {
            $vatTypeExplanation = $configs->getConfigValue('br.invoice_vat_explanation') . ' ' . $this->invoide->getVatContext();
        }

        $subEntries = unserialize($configs->getConfigValue('br.invoice_below_entries'))[$this->lang];

        $vatTypes = unserialize($configs->getConfigValue('br.vat_types'));

        $company = $this->invoide->getOrder()->getCompany();
        $companyContactPerson = $this->invoide->getOrder()->getContact()->getFullName();
        $companyName = $company->getInvoiceName();

        $count = 0;
        $entries = array();
        foreach ($this->invoide->getEntries() as $entry) {
            $product = $entry->getOrderEntry()->getProduct();
            $price = $product->getSignedPrice() / 100;

            if (($price > 0) || ($entry->getInvoiceDescription() !== null && $entry->getInvoiceDescription() != '')) {
                $tax = $this->invoide->getTaxFree() ? 0 : $vatTypes[$product->getVatType()];

                $entries[] = new XmlNode(
                    'entry',
                    null,
                    array(
                        new XmlNode(
                            'description',
                            null,
                            $entry->getInvoiceDescription() === null || $entry->getInvoiceDescription() == '' ? $product->getName() : $entry->getInvoiceDescription()
                        ),
                        new XmlNode('price', null, XmlNode::fromString('<euro/> ' . number_format($price, 2))),
                        new XmlNode('amount', null, $entry->getOrderEntry()->getQuantity() . ''),
                        new XmlNode('vat_type', null, $tax . '%'),
                    )
                );

                $totalExclusive += $price * $entry->getOrderEntry()->getQuantity();

                if (!$this->invoide->getTaxFree()) {
                    $totalVat += ($price * $entry->getOrderEntry()->getQuantity() * $vatTypes[$product->getVatType()]) / 100;
                }

                $count++;
            }
        }

        $percentage = $this->invoide->getOrder()->getAutoDiscountPercentage() / 100;
        $autoDiscount = -$totalExclusive * $percentage;

        $discountTax = $this->invoide->getTaxFree() ? 0 : 21;

        if ($percentage > 0) {
            $entries[] = new XmlNode(
                'entry',
                null,
                array(
                    new XmlNode(
                        'description',
                        null,
                        $this->invoide->getAutoDiscountText() === null || $this->invoide->getAutoDiscountText() == '' ? 'Korting: ' . $percentage . '%' : $this->invoide->getAutoDiscountText()
                    ),
                    new XmlNode('price', null, XmlNode::fromString('<euro/> ' . number_format($autoDiscount, 2))),
                    new XmlNode('amount', null, '1'),
                    new XmlNode('vat_type', null, $discountTax . '%'),
                )
            );

            $totalVat += $autoDiscount * $discountTax / 100;

            $count++;
        }

        while ($count < 8) {
            $entries[] = new XmlNode('empty_line');
            $count++;
        }

        $entries[] = new XmlNode('empty_line');
        $entries[] = new XmlNode('empty_line');

        $discount = $this->invoide->getOrder()->getDiscount() / 100;
        if ($discount != 0) {
            if ($this->invoide->getDiscountText() == '') {
                $entries[] = new XmlNode(
                    'entry',
                    null,
                    array(
                        new XmlNode('description', null, 'Korting'),
                        new XmlNode('price', null, XmlNode::fromString('<euro/> -' . number_format($discount, 2))),
                        new XmlNode('amount', null, '1'),
                        new XmlNode('vat_type', null, $discountTax . '%'),
                    )
                );
            } else {
                $entries[] = new XmlNode(
                    'entry',
                    null,
                    array(
                        new XmlNode('description', null, $this->invoide->getDiscountText()),
                        new XmlNode('price', null, XmlNode::fromString('<euro/> -' . number_format($discount, 2))),
                        new XmlNode('amount', null, '1'),
                        new XmlNode('vat_type', null, $discountTax . '%'),
                    )
                );
            }

            $totalVat += -$discount * $discountTax / 100;
        }

        $totalExclusive -= $discount + $autoDiscount;

        $total = $totalExclusive + $totalVat;

        $xml->append(
            new XmlNode(
                'invoice',
                array(
                    'payment_days' => (String) $paymentDays,
                ),
                array(
                    new XmlNode(
                        'title',
                        null,
                        array(
                            new XmlNode('invoice_number', null, $invoiceNb),
                            new XmlNode('invoice_date', null, $invoiceDate),
                            new XmlNode('expiration_date', null, $dueDate),
                            new XmlNode('vat_client', null, $clientVat),
                            new XmlNode('reference', null, $reference),
                        )
                    ),

                    new XmlNode(
                        'our_union',
                        null,
                        array(
                            new XmlNode('name', null, $unionName),
                            new XmlNode(
                                'address',
                                null,
                                array(
                                    new XmlNode(
                                        'street',
                                        null,
                                        $unionAddressArray['street']
                                    ),
                                    new XmlNode(
                                        'number',
                                        null,
                                        $unionAddressArray['number']
                                    ),
                                    new XmlNode(
                                        'mailbox',
                                        null,
                                        $unionAddressArray['mailbox']
                                    ),
                                    new XmlNode(
                                        'postal',
                                        null,
                                        $unionAddressArray['postal']
                                    ),
                                    new XmlNode(
                                        'city',
                                        null,
                                        $unionAddressArray['city']
                                    ),
                                    new XmlNode(
                                        'country',
                                        null,
                                        $unionAddressArray['country']
                                    ),
                                )
                            ),
                            new XmlNode('logo', null, $logo),
                            new XmlNode('vat_number', null, $unionVat),
                        )
                    ),

                    new XmlNode(
                        'company',
                        array('contact_person' => $companyContactPerson),
                        array(
                            new XmlNode('name', null, $companyName),
                            new XmlNode(
                                'address',
                                null,
                                array(
                                    new XmlNode(
                                        'street',
                                        null,
                                        $company->getInvoiceAddress()->getStreet()
                                    ),
                                    new XmlNode(
                                        'number',
                                        null,
                                        $company->getInvoiceAddress()->getNumber()
                                    ),
                                    new XmlNode(
                                        'mailbox',
                                        null,
                                        $company->getInvoiceAddress()->getMailbox()
                                    ),
                                    new XmlNode(
                                        'postal',
                                        null,
                                        $company->getInvoiceAddress()->getPostal()
                                    ),
                                    new XmlNode(
                                        'city',
                                        null,
                                        $company->getInvoiceAddress()->getCity()
                                    ),
                                    new XmlNode(
                                        'country',
                                        null,
                                        $company->getInvoiceAddress()->getCountry()
                                    ),
                                )
                            ),
                        )
                    ),

                    new XmlNode('entries', null, $entries),

                    new XmlNode(
                        'total',
                        null,
                        array(
                            new XmlNode('vat_type_explanation', null, $vatTypeExplanation),
                            new XmlNode('price_excl', null, XmlNode::fromString('<euro/>' . number_format($totalExclusive, 2))),
                            new XmlNode('price_vat', null, XmlNode::fromString('<euro/>' . number_format($totalVat, 2))),
                            new XmlNode('price_incl', null, XmlNode::fromString('<euro/>' . number_format($total, 2))),
                        )
                    ),

                    new XmlNode('sub_entries', null, $subEntries),

                    new XmlNode('footer'),

                    new XmlNode('sale_conditions_nl'),
                )
            )
        );
    }
}
