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
                        'validators' => array(
                            array('name' => 'UrlValid'),
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
                'attributes' => array(
                    'class' => 'price_non_members',
                ),
                'options'    => array(
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
                'type'       => 'checkbox',
                'name'       => 'membershipDiscount',
                'label'      => 'Member vs non-Member',
                'attributes' => array(
                    'data-help' => 'Enabling this will cause the option to have a member price and a non-member price.',
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'maximum',
                'label'      => 'Maximum amount of tickets',
                'attributes' => array(
                    'class' => 'maximum',
                ),
                'options'    => array(
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
                'type' => 'text',
                'name' => 'limit_per_person_option',
                'label' => 'Limit of tickets per person for this option (0: no limit)',
                'value'    => 0,
                'attributes' => array(
                    'class' => 'maximum',
                ),
                'options' => array(
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
                'type' => 'checkbox',
                'name' => 'visible',
                'label' => 'Option is visible',
                'required' => false,
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $required = $this->get('option')->getValue() && strlen($this->get('option')->getValue()) > 0;

        $specs['option']['required'] = $required;
        $specs['price_members']['required'] = $required;
//        $specs['price_non_members']['required'] = isset($_POST['only_members']) && $_POST['only_members'] ? false : $required;

        return $specs;
    }
}
