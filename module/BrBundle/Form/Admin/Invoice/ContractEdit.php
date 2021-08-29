<?php

namespace BrBundle\Form\Admin\Invoice;

use BrBundle\Entity\Invoice;

/**
 * Edit Invoice
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class ContractEdit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Invoice\Contract';

    /**
     * @var Invoice
     */
    private $invoice;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'company_reference',
                'label'   => 'Company Reference',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        foreach ($this->invoice->getEntries() as $entry) {
            $this->add(
                array(
                    'type'    => 'textarea',
                    'name'    => 'entry_' . $entry->getId(),
                    'label'   => $entry->getOrderEntry()->getProduct()->getName(),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );
        }

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'vat_context',
                'label'   => 'VAT Context',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'eu',
                'label' => 'EU (for the VAT explanation in tax-free invoices)',
            )
        );

        $this->addSubmit('Save', 'invoice_edit');

        if ($this->invoice !== null) {
            $this->bind($this->invoice);
        }
    }

    /**
     * @param  Invoice $invoice
     * @return self
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }
}
