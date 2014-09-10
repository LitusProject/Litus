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

namespace CommonBundle\Form\Admin\Person;

use CommonBundle\Entity\User\Person;

/**
 * Edit Person
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class Edit extends \CommonBundle\Form\Admin\Person\Add
{
    public function init()
    {
        parent::init();

        $this->remove('username');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'system_roles',
            'label'      => 'System Groups',
            'attributes' => array(
                'disabled' => true,
                'multiple' => true,
                'options'  => $this->createRolesArray(true),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'unit_roles',
            'label'      => 'Unit Groups',
            'attributes' => array(
                'disabled' => true,
                'multiple' => true,
                'options'  => $this->createRolesArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'code',
            'label'      => 'Code',
            'attributes' => array(
                'disabled' => true,
            ),
        ));
    }
}
