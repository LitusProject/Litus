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

namespace BrBundle\Form\Admin\Invoice;

use BrBundle\Entity\Invoice;

/**
 * Edit Invoice
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class ContractEdit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Invoice\ContractInvoice';

    /**
     * @var Invoice
     */
    private $invoice;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'company_reference',
            'label'    => 'Company Reference',
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        foreach ($this->invoice->getEntries() as $entry) {
            $this->add(array(
                'type'     => 'textarea',
                'name'     => 'entry_' . $entry->getId(),
                'label'    => $entry->getOrderEntry()->getProduct()->getName(),
                'options'  => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ));
        }

        $this->add(array(
            'type'     => 'textarea',
            'name'     => 'VATContext',
            'label'    => 'VAT Context',
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Save', 'invoice_edit');

        if (null !== $this->invoice) {
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
