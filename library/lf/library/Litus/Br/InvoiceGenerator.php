<?php

namespace Litus\Br;

use \Litus\Util\Xml\XmlGenerator;
use \Litus\Util\Xml\XmlObject;

use \Litus\Util\TmpFile;

use \Litus\Br\DocumentGenerator;
use \Litus\Entity\Br\Contract;

use \Zend\Registry;

class InvoiceGenerator extends DocumentGenerator {

    /**
     * @var \Litus\Entity\Br\Contract
     */
    private $_contract;

    public function __construct(Contract $contract)
    {
        parent::__construct(Registry::get('litus.resourceDirectory') . '/pdf_generators/invoice.xsl',
                            Registry::get('litus.resourceDirectory') . '/pdf/br/' . $contract->getId() . '/invoice.pdf');

        $this->_contract = $contract;
    }

    protected function _generateXml(TmpFile $file)
    {
        $xml = new XmlGenerator($file);

        /** @var $configs \Litus\Repository\General\Config */
        $configs = self::_getConfigRepository();

        $totalExclusive = 0;
        $totalVat = 0;

        // Get the content

        $contractDate = $this->_contract->getDate();
        $invoiceDate = $contractDate->format('j/m/Y');
        $dueDate = $contractDate->add(new \DateInterval('P30D'))->format('j/m/Y');
        $clientVat = $this->_contract->getCompany()->getVatNumber();
        $reference = '/'; // TODO?
        $invoiceNb = '22xxx'; // TODO

        $unionName = $configs->getConfigValue('br.invoice.union_name');
        $unionAddress = self::_formatAddress($configs->getConfigValue('br.invoice.union_address'));
        $unionLogo = $configs->getConfigValue('br.invoice.logo');
        $unionVat = $configs->getConfigValue('br.invoice.union_vat');

        $vatTypeExplanation = $configs->getConfigValue('br.invoice.vat_types');
        $subEntries = $configs->getConfigValue('br.invoice.sub_entries');
        $footer = $configs->getConfigValue('br.invoice.footer');

        $company = $this->_contract->getCompany();
        $companyContactPerson = $company->getFirstName() . ' ' . $company->getLastName();
        $companyName = $company->getName();
        $companyAddress = self::_formatAddress($company->getAddress());

        $count = 0;
        $entries = array();
        foreach ($this->_contract->getComposition() as $part) {
            /** @var $section \Litus\Entity\Br\Contracts\Section */
            $section = $part->getSection();
            $price = $section->getPrice();
            if (($price > 0) ||
                    (($section->getInvoiceDescription() !== null) && ($section->getInvoiceDescription() != ''))) {
                $entries[] = new XmlObject('entry', null,
                    array(
                        new XmlObject('description', null,
                            (($section->getInvoiceDescription() === null) || ($section->getInvoiceDescription() == ''))
                                    ? $section->getName()
                                    : $section->getInvoiceDescription()
                        ),
                        new XmlObject('price', null, $price . ' <euro/>'),
                        new XmlObject('vat_type', null, $section->getVatType())
                    )
                );

                $totalExclusive += $price;
                $totalVat += ($price * $section->getVat()) / 100;

                $count++;
            }
        }

        while($count < 8) {
            $entries[] = new XmlObject('empty_line');
            $count++;
        }

        // Append two more empty lines
        $entries[] = new XmlObject('empty_line');
        $entries[] = new XmlObject('empty_line');

        $discount = $this->_contract->getDiscount();
        if($discount != 0) {
            $entries[] = new XmlObject('entry', null,
                array(
                    new XmlObject('description', null, '-' . $discount . '%'),
                    new XmlObject('price', null, ( -($discount * $totalExclusive) / 100 ) . ' <euro/>'),
                    new XmlObject('vat_type', null, ' ')
                )
            );
        }

        $totalExclusive -= ($discount * $totalExclusive) / 100;
        $totalVat -= ($discount * $totalVat) / 100;

        $total = $totalExclusive + $totalVat;

        $xml->append(new XmlObject('invoice', null,
            array(
                // children of <invoice>
                new XmlObject('title', null,
                    array(
                        // children of <title>
                        new XmlObject('invoice_number', null, $invoiceNb),
                        new XmlObject('invoice_date', null, $invoiceDate),
                        new XmlObject('expiration_date', null, $dueDate),
                        new XmlObject('vat_client', null, $clientVat),
                        new XmlObject('reference', null, $reference)
                    )
                ),

                new XmlObject('our_union', null,
                    array(
                        // children of <our_union>
                        new XmlObject('name', null, $unionName),
                        new XmlObject('address', null, $unionAddress),
                        new XmlObject('logo', null, $unionLogo),
                        new XmlObject('vat_number', null, $unionVat)
                    )
                ),

                new XmlObject('company', array('contact_person' => $companyContactPerson),
                    array(
                        // children of <company>
                        new XmlObject('name', null, $companyName),
                        new XmlObject('address', null, $companyAddress)
                    )
                ),

                new XmlObject('entries', null, $entries),

                new XmlObject('total', null,
                    array(
                        // children of <total>
                        new XmlObject('vat_type_explanation', null, $vatTypeExplanation),
                        new XmlObject('price_excl', null, $totalExclusive . ' <euro/>'),
                        new XmlObject('price_vat', null, $totalVat . ' <euro/>'),
                        new XmlObject('price_incl', null, $total . ' <euro/>')
                    )
                ),

                new XmlObject('sub_entries', null, $subEntries),

                new XmlObject('footer', null, $footer)
            )
        ));
    }
}
