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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Group;

use FormBundle\Entity\Node\Group;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('start_form');

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
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name'    => 'DateCompare',
                            'options' => array(
                                'first_date' => 'start_date',
                                'format'     => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'active',
            'label' => 'Active',
        ));

        $this->add(array(
            'type'    => 'text',
            'name'    => 'max',
            'label'   => 'Total Max Entries',
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'Int'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'non_member',
            'label' => 'Allow Entry Without Login',
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'editable_by_user',
            'label' => 'Allow Users To Edit Their Info',
        ));

        $this->remove('submit')
            ->addSubmit('Save', 'form_edit');
    }
}
