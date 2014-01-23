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

namespace Admin\Form\Contract;

use \Litus\Entity\Br\Contract;

use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Hidden;
use \Zend\Form\Element\Text;

/**
 * Edit Contract
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add {

    public function __construct(Contract $contract, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');

        $field = new Hidden('id');
        $field->setValue($contract->getId());
        $this->addElement($field);

        $field = new Text('contract_nb');
        $field->setLabel('Contract number')
            ->setRequired();
        $this->addElement($field);

        if($contract->isSigned()) {
            $field = new Text('invoice_nb');
            $field->setLabel('Invoice number')
                ->setRequired()
                ->setValue($contract->getInvoiceNb())
                ->setAttrib('disabled', 'disabled');
            $this->addElement($field);
        }

        $field = new Submit('Save');
        $field->setValue('Save')
            ->setAttrib('class', 'contracts_edit');
        $this->addElement($field);

        $this->populate(
            array(
                'company'       => $contract->getCompany()->getId(),
                'discount'      => $contract->getDiscount(),
                'title'         => $contract->getTitle(),
                'sections'      => $this->_getActiveSections($contract),
                'contract_nb'   => $contract->getContractNb()
            )
        );
    }

    private function _getActiveSections(Contract $contract)
    {
        $return = array();
        foreach ($contract->getComposition() as $contractComposition)
            $return[] = $contractComposition->getSection()->getId();
        return $return;
    }
}
