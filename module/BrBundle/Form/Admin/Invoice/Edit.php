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

namespace BrBundle\Form\Admin\Invoice;

use BrBundle\Entity\Contract\Section,
    BrBundle\Entity\Invoice,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    Zend\Validator\Float as FloatValidator;

/**
 * Edit Invoice
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \BrBundle\Entity\Invoice    $invoice       The invoice to edit
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Invoice $invoice, $options = null)
    {
        parent::__construct($options);

        $this->_createFromInvoice($invoice);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'invoice_edit');
        $this->add($field);
    }

    private function _createFromInvoice(Invoice $invoice)
    {
        foreach ($invoice->getEntries() as $entry) {
            $field = new Textarea('entry_' . $entry->getId());
            $field->setLabel($entry->getOrderEntry()->getProduct()->getName())
                ->setValue($entry->getInvoiceText())
                ->setRequired(false);
            $this->add($field);
        }

        $field = new Textarea('VATContext');
        $field->setLabel("VAT Context")
            ->setValue($invoice->getVATContext())
            ->setRequired(false);
        $this->add($field);
    }
}
