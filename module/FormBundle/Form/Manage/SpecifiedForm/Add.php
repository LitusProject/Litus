<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Manage\SpecifiedForm;

use CommonBundle\Component\Validator\Typeahead\Person as PersonTypeaheadValidator;

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

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'is_guest',
            'label'      => 'Is Guest',
            'attributes' => array(
                'id' => 'is_guest',
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'person_form',
            'label'      => 'Person',
            'elements'   => array(
                array(
                    'type'       => 'typeahead',
                    'name'       => 'person',
                    'label'      => 'Person',
                    'required'   => true,
                    'options'    => array(
                        'input' => array(
                            'validators' => array(
                                new PersonTypeaheadValidator($this->getEntityManager()),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'fieldset',
            'name'       => 'guest_form',
            'label'      => 'Guest',
            'elements'   => array(
                array(
                    'type'       => 'text',
                    'name'       => 'guest_first_name',
                    'label'      => 'First Name',
                    'required'   => true,
                    'attributes' => array(
                        'id' => 'guest_first_name',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'guest_last_name',
                    'label'      => 'Last Name',
                    'required'   => true,
                    'attributes' => array(
                        'id' => 'guest_last_name',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'text',
                    'name'       => 'guest_email',
                    'label'      => 'Email',
                    'required'   => true,
                    'attributes' => array(
                        'id' => 'guest_email',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters'  => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                array('name' => 'EmailAddress'),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'fieldset',
            'name'  => 'fields_form',
            'label' => 'Form',
        ));

        $fieldsForm = $this->get('fields_form');

        foreach ($this->getElements() as $name => $element) {
            if ($name == 'submit' || $name == 'is_guest' || $name = 'csrf') {
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
