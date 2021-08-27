<?php

namespace TicketBundle\Form\Admin\Event;

use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Add Option
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Option extends \CommonBundle\Component\Form\Fieldset implements InputFilterProviderInterface
{
    public function init()
    {
        parent::init();

        $this->setLabel('Option');

        $this->add(
            array(
                'type'     => 'hidden',
                'name'     => 'option_id',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'option',
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
                'type'     => 'text',
                'name'     => 'price_members',
                'label'    => 'Price Members',
                'required' => true,
                'options'  => array(
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
                'name'       => 'price_non_members',
                'label'      => 'Price Non Members',
                'required'   => true,
                'attributes' => array(
                    'class' => 'price_non_members',
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
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $required = $this->get('option')->getValue() && strlen($this->get('option')->getValue()) > 0;

        $specs['option']['required'] = $required;
        $specs['price_members']['required'] = $required;
        $specs['price_non_members']['required'] = isset($_POST['only_members']) && $_POST['only_members'] ? false : $required;

        return $specs;
    }
}
