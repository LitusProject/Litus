<?php

namespace Admin\Form\Contract;

use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Entity\Br\Contract;

use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Hidden;

class Edit extends Add {

    public function __construct(Contract $contract, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');

        $field = new Hidden('id');
        $field->setValue($contract->getId());
        $this->addElement($field);

        $field = new Submit('Save');
        $field->setValue('Save')
            ->setAttrib('class', 'contracts_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populate(
            array(
                'company'   => $contract->getCompany()->getId(),
                'discount'  => $contract->getDiscount(),
                'title'     => $contract->getTitle(),
                'sections'  => $this->_getActiveSections($contract)
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
