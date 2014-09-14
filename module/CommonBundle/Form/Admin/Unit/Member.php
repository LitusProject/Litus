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

namespace CommonBundle\Form\Admin\Unit;

use CommonBundle\Component\Validator\Academic as AcademicValidator;

/**
 * The form used to add a member to a unit.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Member extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'person_name',
            'label'      => 'Name',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => 'personSearch',
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'coordinator',
            'label' => 'Coordiantor',
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'person_id',
            'attributes' => array(
                'id' => 'personId',
            ),
        ));

        $this->addSubmit('Add', 'unit_member_add');
    }

    public function getInputFilterSpecification()
    {
        if (!isset($this->data['person_id']) || '' == $this->data['person_id']) {
            return array(
                array(
                    'name' => 'person_name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new AcademicValidator(
                            $this->getEntityManager(),
                            array(
                                'byId' => false,
                            )
                        ),
                    ),
                ),
            );
        } else {
            return array(
                array(
                    'name' => 'person_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new AcademicValidator(
                            $this->getEntityManager(),
                            array(
                                'byId' => true,
                            )
                        ),
                    ),
                ),
            );
        }
    }
}
