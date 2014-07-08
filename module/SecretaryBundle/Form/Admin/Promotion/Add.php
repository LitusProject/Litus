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

namespace SecretaryBundle\Form\Admin\Promotion;

use CommonBundle\Component\Form\Collection;

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

        $requireAcademic = true; //isset($this->data['academic_add']) && $this->data['academic_add']

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'academic_add',
            'label'      => 'Academic',
            'value'      => true,
        ));

        $academic = new Collection('academic');
        $academic->setLabel('Academic');
        $this->add($academic);

        $academic->add(array(
            'type'       => 'hidden',
            'name'       => 'academic_id',
            'required'   => $requireAcademic,
            'attributes' => array(
                'id' => 'academicId',
            ),
            'options' => array(
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

        $academic->add(array(
            'type'       => 'text',
            'name'       => 'academic_name',
            'label'      => 'Academic',
            'required'   => $requireAcademic,
            'attributes' => array(
                'id' => 'academicSearch',
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
            ),
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $external = new Collection('external');
        $external->setLabel('External');
        $this->add($external);

        $external->add(array(
            'type'       => 'text',
            'name'       => 'external_first_name',
            'label'      => 'First Name',
            'required'   => !$requireAcademic,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $external->add(array(
            'type'       => 'text',
            'name'       => 'external_last_name',
            'label'      => 'Last Name',
            'required'   => !$requireAcademic,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $external->add(array(
            'type'       => 'text',
            'name'       => 'external_email',
            'label'      => 'Email',
            'required'   => !$requireAcademic,
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'EmailAddress',
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'add');
    }
}
