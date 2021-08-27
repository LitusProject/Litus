<?php

namespace CudiBundle\Form\Admin\Sale\Article\Sale;

/**
 * Add Sale
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'sale_to',
                'label'      => 'Sale To',
                'required'   => true,
                'attributes' => array(
                    'options' => array(
                        'prof'  => 'Prof',
                        'other' => 'Other',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'number',
                'label'      => 'Number',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 75px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                            ),
                            array(
                                'name'    => 'GreaterThan',
                                'options' => array(
                                    'min' => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'price',
                'label'      => 'Price',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 75px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'name',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 300px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Sale', 'sale');
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['price']['required'] = !isset($this->data['sale_to']) || $this->data['sale_to'] == 'other';

        return $specs;
    }
}
