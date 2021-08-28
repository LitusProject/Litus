<?php

namespace FormBundle\Form\Manage\SpecifiedForm;

/**
 * Specifield Form Add
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \FormBundle\Form\SpecifiedForm\Add
{
    public function init()
    {
        parent::init();

        $this->remove('first_name');
        $this->remove('last_name');
        $this->remove('email');
        $this->remove('save_as_draft');

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'is_guest',
                'label'      => 'Is Guest',
                'attributes' => array(
                    'id' => 'is_guest',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'person_form',
                'label'    => 'Person',
                'elements' => array(
                    array(
                        'type'     => 'typeahead',
                        'name'     => 'person',
                        'label'    => 'Person',
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
                'type'     => 'fieldset',
                'name'     => 'guest_form',
                'label'    => 'Guest',
                'elements' => array(
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
                    ),
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
                    ),
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
                                    array('name' => 'EmailAddress'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'fieldset',
                'name'  => 'fields_form',
                'label' => 'Form',
            )
        );

        $fieldsForm = $this->get('fields_form');

        foreach ($this->getElements() as $name => $element) {
            if (strpos($name, 'field-') !== 0) {
                continue;
            }

            $this->remove($name);
            $fieldsForm->add($element);
        }
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $isGuest = isset($this->data['is_guest']) && $this->data['is_guest'];

        if ($isGuest) {
            unset($specs['person_form']);
        } else {
            unset($specs['guest_form']);
        }

        return $specs;
    }
}
