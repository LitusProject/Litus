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

use CommonBundle\Component\Validator\Typeahead\Person as PersonTypeaheadValidator;

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
            'type'       => 'typeahead',
            'name'       => 'academic',
            'label'      => 'Academic',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new PersonTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'date',
            'name'     => 'start_date',
            'label'    => 'Start Date',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'date',
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
