<?php

namespace ShopBundle\Form\Shop;

class Consume extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'username',
                'label'      => 'Student Number',
                'required'   => true,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'id'           => 'username',
                    'placeholder'  => 'Student Number',
                ),
            )
        );

        $this->addSubmit('Consume', 'shop_consume', 'consume', array('id' => 'shop_consume'));
    }
}
