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

namespace SyllabusBundle\Form\Admin\Group;

use LogicException;

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

        if (null === $this->group) {
            throw new LogicException('Cannot edit null group');
        }

        if ((!$this->isPocGroup) or $this->isPocGroup === null) {
            $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'poc_group',
            'label' => 'Is POC group this year?',
        ));
        } else {
            $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'poc_group',
            'label' => 'Is POC group this year? ',
            'value'      => true,
            'attributes' => array(
            'disabled' => 1,),
            ));
        }

        $this->remove('submit');
        $this->addSubmit('Save', 'edit');

        $this->bind($this->group);
    }
}
