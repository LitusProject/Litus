<?php

namespace CudiBundle\Form\Admin\Supplier;

use CudiBundle\Entity\Supplier;
/**
 * Add Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Supplier';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
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
                'type'       => 'text',
                'name'       => 'phone_number',
                'label'      => 'Phone Number',
                'attributes' => array(
                    'placeholder' => '+CCAAANNNNNN',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'PhoneNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'contact',
                'label' => 'Contact',
            )
        );

        $this->add(
            array(
                'type'  => 'common_address_add',
                'name'  => 'address',
                'label' => 'Address',
            )
        );

        $this->add(
            array(
                'type'  => 'text',
                'name'  => 'vat_number',
                'label' => 'VAT Number',
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'template',
                'label'      => 'Template',
                'required'   => true,
                'attributes' => array(
                    'options' => Supplier::$possibleTemplates,
                ),
            )
        );

        $this->addSubmit('Add', 'supplier_add');
    }
}
