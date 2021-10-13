<?php

namespace SecretaryBundle\Form\Admin\Promotion;

/**
 * Add Promotion form
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
                'type'       => 'checkbox',
                'name'       => 'academic_add',
                'label'      => 'Academic',
                'value'      => true,
                'attributes' => array(
                    'id' => 'academic_add',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'academic',
                'label'      => 'Academic',
                'attributes' => array(
                    'id' => 'academic',
                ),
                'elements'   => array(
                    array(
                        'type'     => 'typeahead',
                        'name'     => 'academic',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'validators' => array(
                                    array('name' => 'TypeaheadPerson'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'fieldset',
                'name'       => 'external',
                'label'      => 'External',
                'attributes' => array(
                    'id' => 'external',
                ),
                'elements'   => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'external_first_name',
                        'label'    => 'First Name',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'external_last_name',
                        'label'    => 'Last Name',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'external_email',
                        'label'    => 'Email',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'EmailAddress'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    public function getInputFilterSpecification()
    {
        $specification = parent::getInputFilterSpecification();

        if ($this->data['academic_add']) {
            unset($specification['external']);
        } else {
            unset($specification['academic']);
        }

        return $specification;
    }
}
