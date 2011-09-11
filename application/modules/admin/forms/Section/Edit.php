<?php

namespace Admin\Form\Section;

use \Litus\Form\Decorator\ButtonDecorator;
use \Litus\Entity\Br\Contracts\Section;

use \Zend\Form\Element\Submit;

class Edit extends Add
{
    public function __construct(Section $section, $options = null)
    {
        parent::__construct($options);

        $this->removeElement('submit');

        $field = new Submit('submit');
        $field->setLabel('Save changes')
            ->setAttrib('class', 'sections_edit')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populate(
            array(
                'name' => $section->getName(),
                'price' => $section->getPrice(),
                'vat_type' => $section->getVatType(),
                'content' => $section->getContent(),
                'invoice_description' => $section->getInvoiceDescription()
            )
        );
    }
}
