<?php

namespace PromBundle\Form\Admin\ReservationCode;

/**
 * Add External
 *
 * @author Matthias Swiggers <matthias.swiggers@studentit.be>
 */
class External extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PromBundle\Hydrator\Bus\ReservationCode\External';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'first_name',
                'label'    => 'First Name',
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
                'name'     => 'last_name',
                'label'    => 'Last Name',
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
                'name'     => 'email',
                'label'    => 'Email',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'EmailAddress',
                            ),
                            array(
                                'name' => 'EntryExternal',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'number_tickets',
                'label'      => 'Number of tickets',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getNumberOptions(),
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'external_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'code_add',
                ),
            )
        );
    }

    private function getNumberOptions()
    {
        $nb = 10;
        $options = array();
        for ($i = 0; $i <= $nb; $i++) {
            $options[$i] = $i;
        }

        return $options;
    }
}
