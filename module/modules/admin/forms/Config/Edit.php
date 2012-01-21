<?php

namespace Admin\Form\Config;

use \Zend\Form\Element\Hidden;

use \Litus\Entity\Config\Config;

class Edit extends Add
{

    public function __construct(Config $config, $options = null)
    {
        parent::__construct($options);

        $this->populate(
            array(
                'description' => $config->getDescription(),
                'value' => $config->getValue()
            )
        );

        $field = $this->getElement('submit');
        $field->setLabel('Edit');

        $field = $this->getElement('prefix');
        $this->removeElement($field);

        $field = $this->getElement('name');
        $this->removeElement($field);

        $field = new Hidden('name');
        $field->setValue($config->getKey());
        $this->addElement($field);
    }
}
