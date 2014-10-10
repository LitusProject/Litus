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

namespace ShiftBundle\Form\Admin\Subscription;

use CommonBundle\Component\Validator\Academic as AcademicValidator;

/**
 * Add a subscription to a shift
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShiftBundle\Hydrator\Subscriber';

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
            'options' => array(
                'input' => array(
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
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'person_id',
            'required'   => true,
            'attributes' => array(
                'id' => 'personId',
            ),
            'options' => array(
                'input' => array(
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
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'responsible',
            'label' => 'Responsible',
        ));

        $this->addSubmit('Add', 'add');
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if (!isset($this->data['person_id']) || '' == $this->data['person_id']) {
            unset($specs['person_id']);
        } else {
            unset($specs['person_name']);
        }

        return $specs;
    }
}
