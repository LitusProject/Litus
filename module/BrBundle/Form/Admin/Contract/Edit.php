<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace BrBundle\Form\Admin\Contract;

use BrBundle\Entity\Contract,
    CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to edit an existing contract
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add {

    public function __construct(EntityManager $entityManager, Contract $contract, $options = null)
    {
        parent::__construct($entityManager, $options);

        $this->remove('submit');

        $field = new Hidden('id');
        $field->setValue($contract->getId());
        $this->add($field);

        $field = new Text('contract_nb');
        $field->setLabel('Contract number')
            ->setRequired(true);
        $this->add($field);

        if($contract->isSigned()) {
            $field = new Text('invoice_nb');
            $field->setLabel('Invoice number')
                ->setRequired(true)
                ->setValue($contract->getInvoiceNb())
                ->setAttribute('disabled', 'disabled');
            $this->add($field);
        }

        $field = new Submit('Save');
        $field->setValue('Save')
            ->setAttribute('class', 'contracts_edit');
        $this->add($field);

        $this->_populateFromContract($contract);
    }
}
