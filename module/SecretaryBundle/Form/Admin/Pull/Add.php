<?php

namespace SecretaryBundle\Form\Admin\Pull;

use SecretaryBundle\Entity\Pull;

class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SecretaryBundle\Hydrator\Pull';

    protected $pull;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'study_nl',
                'label'      => 'Study NL',
                'attributes' => array(
                    'id' => 'study_nl',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'study_en',
                'label'      => 'Study EN',
                'attributes' => array(
                    'id' => 'study_en',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            ),
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'amount_available',
                'label'      => 'Amount in stock',
                'attributes' => array(
                    'id' => 'amount_available',
                ),
                'required'   => false,
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
            ),
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'available',
                'label'      => 'Available to book',
                'attributes' => array(
                    'id' => 'available',
                ),
            )
        );

        $this->addSubmit('Add', 'pull_add');

        if ($this->pull !== null) {
            $this->bind($this->pull);
        }
    }

    public function setPull(Pull $pull)
    {
        $this->pull = $pull;

        return $this;
    }
}
