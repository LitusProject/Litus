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

namespace DoorBundle\Form\Admin\Rule;

/**
 * Add Key
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'DoorBundle\Hydrator\Rule';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'academic_id',
            'attributes' => array(
                'id' => 'personId',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'academic',
            'label'      => 'Academic',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
                'id'           => 'academicSearch',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'start_date',
            'label'    => 'Start Date',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'end_date',
            'label'    => 'End Date',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'time',
            'name'     => 'start_time',
            'label'    => 'Start Time',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'time',
            'name'     => 'end_time',
            'label'    => 'End Time',
            'required' => true,
        ));

        $this->addSubmit('Add', 'rule_add');
    }
}
